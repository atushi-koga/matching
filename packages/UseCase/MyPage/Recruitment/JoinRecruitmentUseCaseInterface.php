<?php
declare(strict_types=1);

namespace packages\UseCase\MyPage\Recruitment;

use packages\UseCase\Top\DetailRecruitmentRequest;

interface JoinRecruitmentUseCaseInterface
{
    /**
     * @param DetailRecruitmentRequest $request
     * @return void
     */
    public function handle(DetailRecruitmentRequest $request);
}
