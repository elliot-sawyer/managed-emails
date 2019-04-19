<?php
namespace ElliotSawyer\EmailManagement;
use SilverStripe\Core\ClassInfo;
use Symbiote\QueuedJobs\Services\QueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
if (!interface_exists(QueuedJob::class)) {
    return;
}
class SendManagedEmailJob extends AbstractQueuedJob
{
    private $title = 'Send managed email job';
    public function getTitle()
    {
        return $this->title;
    }

    public function process()
    {
        $email = $this->Email;
        if(!$this->Email) {
            $this->addMessage('Email not loaded');
        }
        if($email instanceof \SilverStripe\Control\Email\Email) {
            $email->send();
        }
        $this->isComplete = true;
    }
}
