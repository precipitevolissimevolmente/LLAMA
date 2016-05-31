<?php
function sendEmailWithAttachment($file_name, $fileNameWithPath)
{

    $from_email = 'terrainfoplus.md@terrainfoplus.md'; //sender email
    $recipient_email = 'llama.results@gmail.com'; //recipient email
    $subject = 'LLAMA results for ' . $file_name; //subject of email
    $message = 'results were attached to this email'; //message body

    //get file details we need
    $file_size = filesize($fileNameWithPath);
    $file_type = "application/json";

    $user_email = $from_email;

    //read from the uploaded file & base64_encode content for the mail
    $handle = fopen($fileNameWithPath, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $encoded_content = chunk_split(base64_encode($content));


    $boundary = md5("sanwebe");
    //header
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From:" . $from_email . "\r\n";
    $headers .= "Reply-To: " . $user_email . "" . "\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n";

    //plain text
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode($message));

    //attachment
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n";
    $body .= $encoded_content;

    $sentMail = @mail($recipient_email, $subject, $body, $headers);
    if ($sentMail) //output success or failure messages
    {
        logg('Thank you for your email');
    } else {
        logg('Could not send mail! Please check your PHP mail configuration.');
    }

}