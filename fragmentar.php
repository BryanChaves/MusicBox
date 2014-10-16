<?php





$time = '00:00:30';
exec("ffmpeg -i file.mp3 -acodec copy -t " . $time ." -ss 00:00:00 Betico1.mp3");










?>