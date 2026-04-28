<?php
// Generate a soft notification ding WAV file
$sampleRate = 22050;
$duration = 0.45;
$numSamples = (int)($sampleRate * $duration);

$data = '';
for ($i = 0; $i < $numSamples; $i++) {
    $t = $i / $sampleRate;
    $envelope = exp(-7 * $t);
    $freq = 880 + 440 * exp(-10 * $t);
    $sample = (int)(sin(2 * M_PI * $freq * $t) * 32767 * $envelope);
    $data .= pack('v', $sample & 0xFFFF);
}

$dataSize = strlen($data);
$header  = 'RIFF';
$header .= pack('V', 36 + $dataSize);
$header .= 'WAVE';
$header .= 'fmt ';
$header .= pack('V', 16);
$header .= pack('v', 1);
$header .= pack('v', 1);
$header .= pack('V', $sampleRate);
$header .= pack('V', $sampleRate * 2);
$header .= pack('v', 2);
$header .= pack('v', 16);
$header .= 'data';
$header .= pack('V', $dataSize);

file_put_contents(__DIR__ . '/public/sounds/notify.wav', $header . $data);
echo 'Sound created: ' . (36 + $dataSize) . ' bytes' . PHP_EOL;
