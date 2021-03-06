<?php

namespace Tests\Feature\Mypage;

use App\Eloquent\EloquentUser;
use packages\Domain\Domain\Recruitment\Recruitment;
use packages\Domain\Domain\Recruitment\RecruitmentRepositoryInterface;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewRecruitmentTest extends TestCase
{
    use RefreshDatabase;

    /** @var EloquentUser */
    private $user;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->setInitData();
        $this->user = $this->login();
    }

    /**
     * 募集内容登録画面が表示される事を確認
     *
     * @return void
     */
    public function testCanDisplayCreateForm()
    {
        $this->get('/manage/event/create')
             ->assertStatus(200)
             ->assertViewIs('manage.new_event.form');
    }


    /**
     * 募集内容情報が正常にDB登録される事を確認
     */
    public function testCanCreateRecruitment()
    {
        $request = [
            'title'       => '丹沢大山に登ろう',
            'mount'       => '丹沢大山',
            'prefecture'  => 14,
            'schedule'    => '伊勢原駅→登山開始→下山完了',
            'date'        => '2019-10-3',
            'capacity'    => 5,
            'deadline'    => '2019-9-28',
            'requirement' => 'ルールを守れる方',
            'belongings'  => '昼食、登山靴、着替類',
            'notes'       => '自己責任でお願いします',
            'create_id'   => $this->user->id,
        ];

        $recruitment = Recruitment::ofByArray($request);
        $recruitment->setId(1);

        $mock = $this->createMock(RecruitmentRepositoryInterface::class);
        $mock->expects($this->once())
             ->method('create')
             ->with(Recruitment::ofByArray($request))
             ->willReturn($recruitment);

        $this->instance(RecruitmentRepositoryInterface::class, $mock);

        $this->post('/manage/event/create', $request)
             ->assertRedirect('/manage/event/create/finish');
    }

    public function testCanDisplayCreateFinish()
    {
        $this->get('/manage/event/create/finish')
             ->assertStatus(200)
             ->assertViewIs('manage.new_event.finish');
    }
}
