<?php
declare(strict_types=1);

namespace packages\Infrustructure\Recruitment;

use App\Eloquent\EloquentRecruitment;
use App\Eloquent\EloquentUser;
use App\Eloquent\EloquentUsersRecruitment;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use packages\Domain\Domain\Common\Date;
use packages\Domain\Domain\Common\Prefecture;
use packages\Domain\Domain\Recruitment\Capacity;
use packages\Domain\Domain\Recruitment\DetailRecruitment;
use packages\Domain\Domain\Recruitment\Recruitment;
use packages\Domain\Domain\Recruitment\RecruitmentRepositoryInterface;
use packages\Domain\Domain\Recruitment\TopRecruitment;
use packages\Domain\Domain\User\BirthDay;
use packages\Domain\Domain\User\BrowsingRestriction;
use packages\Domain\Domain\User\Gender;
use packages\Domain\Domain\User\ParticipantInfo;
use packages\Domain\Domain\User\User;
use packages\Domain\Domain\User\UserStatus;
use packages\UseCase\MyPage\Recruitment\DetailRecruitmentRequest;

class RecruitmentRepository implements RecruitmentRepositoryInterface
{
    /**
     * @param Recruitment $recruitment
     * @return Recruitment
     * @throws \Exception
     * @throws \Throwable
     */
    public function create(Recruitment $recruitment): Recruitment
    {
        return DB::transaction(
            function () use ($recruitment) {
                /** @var EloquentRecruitment $recruitmentRecord */
                $recruitmentRecord = EloquentRecruitment::query()
                    ->create(
                        [
                            'title'       => $recruitment->getTitle(),
                            'mount'       => $recruitment->getMount(),
                            'prefecture'  => $recruitment->getPrefectureKey(),
                            'schedule'    => $recruitment->getSchedule(),
                            'date'        => $recruitment->getFormatDate(),
                            'capacity'    => $recruitment->getCapacityValue(),
                            'deadline'    => $recruitment->getFormatDeadline(),
                            'requirement' => $recruitment->getRequirement(),
                            'belongings'  => $recruitment->getBelongings(),
                            'notes'       => $recruitment->getNotes(),
                            'create_id'   => $recruitment->getCreateUserId(),
                        ]);

                EloquentUsersRecruitment::query()
                    ->create(
                        [
                            'user_id'        => $recruitmentRecord->create_id,
                            'recruitment_id' => $recruitmentRecord->id,
                            'is_accepted'    => true,
                            'user_status'    => UserStatus::ADMIN_STATUS,
                            'created_at'     => Carbon::now(),
                        ]);

                $newRecruitment = $recruitmentRecord->toModel();
                $newRecruitment->setId($recruitmentRecord->id);

                return $newRecruitment;
            });
    }

    /**
     * 公開範囲の募集情報を表示する
     *
     * @param BrowsingRestriction $criteria
     * @return TopRecruitment[]
     */
    public function searchForTop(BrowsingRestriction $criteria): array
    {
        $results = EloquentRecruitment::query()
            ->where(function ($q/** @var \Illuminate\Database\Eloquent\Builder $q */) use ($criteria) {
                $q->whereNull('gender_limit')
                    ->orWhere('gender_limit', $criteria->gender->getKey());
            })
            ->where(function ($q2/** @var \Illuminate\Database\Eloquent\Builder $q2 */) use ($criteria) {
                $q2->where(function ($qq1/** @var \Illuminate\Database\Eloquent\Builder $qq1 */) use ($criteria) {
                    $qq1->whereNull('minimum_age')
                        ->orWhereRaw('? >= minimum_age', [$criteria->age->getValue()]);
                });
                $q2->where(function ($qq2/** @var \Illuminate\Database\Eloquent\Builder $qq2 */) use ($criteria) {
                    $qq2->whereNull('upper_age')
                        ->orWhereRaw('? <= upper_age', [$criteria->age->getValue()]);
                });
            })
            ->orderBy('date')
            ->get();

        $topRecruitments = [];
        foreach ($results as $r/** @var EloquentRecruitment $r */) {
            $recruitment = $r->toModel();
            $recruitment->setId($r->id);
            $count = $this->entryRecruitments($recruitment)
                ->count();
            $recruitment->setEntryCount($count);

            $createUser = $r->createUser->toModel();

            $topRecruitments[] = TopRecruitment::ofByArray([
                'recruitment' => $recruitment,
                'createUser'  => $createUser,
            ]);
        }

        return $topRecruitments;
    }

    public function entryRecruitments(Recruitment $recruitment): Collection
    {
        return EloquentUsersRecruitment::query()
            ->where('recruitment_id', $recruitment->getId())
            ->where('user_status', '<>', UserStatus::ADMIN_STATUS)
            ->get();
    }

    /**
     * @param DetailRecruitmentRequest $request
     * @return DetailRecruitment
     */
    public function detail(DetailRecruitmentRequest $request): DetailRecruitment
    {
        /** @var EloquentRecruitment $recruitmentRecord */
        $recruitmentRecord = EloquentRecruitment::query()
            ->findOrFail($request->recruitment_id);

        $recruitment = $recruitmentRecord->toModel();
        $recruitment->setId($recruitmentRecord->id);

        $createUserRecord = EloquentUser::query()
            ->findOrFail($recruitmentRecord->create_id);

        $createUser = new ParticipantInfo(
            $createUserRecord->id,
            $createUserRecord->nickname,
            Gender::of($createUserRecord->gender),
            Prefecture::of($createUserRecord->prefecture),
            Birthday::of($createUserRecord->birthday)
        );

        $participantInfoList = [];
        foreach ($recruitmentRecord->usersRecruitment as $userRecruitment) {
            $user            = $userRecruitment->user;
            $participantInfo = new ParticipantInfo(
                $user->id,
                $user->nickname,
                Gender::of($user->gender),
                Prefecture::of($user->prefecture),
                Birthday::of($user->birthday)
            );

            $participantInfoList[] = $participantInfo;
        }

        $detailRecruitment = new DetailRecruitment(
            $recruitment,
            $createUser,
            $request->browsing_user_id,
            $participantInfoList
        );

        return $detailRecruitment;
    }
}
