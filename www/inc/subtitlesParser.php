<?php
  function parse_time($time_str) {
    $time_str = preg_replace('/:/', '', $time_str);
    $time_str = preg_replace('/,/', '.', $time_str);
    return (float) $time_str;
  }

  function parse_file($file) {
    if (! $file) {
      return false;
    }
    $file_content = file($file);
    $list = [];
    $orig = "<span class='sub' data-init='%s' data-end='%s'>%s</span>";
    $temp = "";
    foreach ($file_content as $line) {
      if (preg_match("/^[\d]{1,}[\n\r]/m", $line)) {
        array_push($list, $temp);
        $temp = $orig;
      } elseif (preg_match("/^[\d]{2}:[\d]{2}:[\d]{2},[\d]{3} --> [\d]{2}:[\d]{2}:[\d]{2},[\d]{3}[\n\r]/m", $line)) {
        $data = preg_split("/ --> /", $line);
        $temp = sprintf($temp, parse_time($data[0]), parse_time($data[1]), "%s");
      } elseif (preg_match("/^[\n\r]/m", $line)) {
        $temp = sprintf($temp, "");
      } else {
        $temp = sprintf($temp, $line."<br>%s");
      }
    }
    return $list;
  }
?>
