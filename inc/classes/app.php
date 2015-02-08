<?php

class app {

  public function systemMail($to, $subject, $message){
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $msgheader = "<html><head></head><body style='font-family: sans-serif;'>";
    $msgfooter = "</body></html>";
    mail($to, $subject, $msgheader.$message.$msgfooter, $headers, EMAIL_FROM);
  }

}
