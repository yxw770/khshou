<?php

namespace app\common\queue;

use think\queue\Job;
use Mailermaster\PHPMailer;

class QueueClient
{
    public function sendMAIL(Job $job, $data)
    {
        $isJobDone = $this->send_email($data['email'], $data['title'], $data['msg']);
        if ($isJobDone) {
            $job->delete();
        } else {
            if ($job->attempts() > 3) {
                $job->delete();
            }
        }
    }

    private function send_email($email, $title, $msg, $siteurl = '')
    {
        $smtp_arr = cache('adminconfig');
        $site_arr = cache('guestconfig');
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->Username = $smtp_arr['stmpeemail'];
        $mail->Password = $smtp_arr['sttmppwd'];
        $mail->Host = $smtp_arr['stmp'];
        $mail->isHTML(true);
        $mail->setFrom($smtp_arr['stmpeemail'], $site_arr['siteurl']);
        $mail->Subject = $title;
        $mail->Body = $msg;
        $mail->addAddress($email);
        $res1 = $mail->send();
        if ($res1) {
            return true;
        } else {
            return false;
        }
    }
}