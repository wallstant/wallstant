<?php
function time_ago($timestamp){
    //type cast, current time, difference in timestamps
    $timestamp      = (int) $timestamp;
    $current_time   = time();
    $diff           = $current_time - $timestamp;
    //intervals in seconds
    $intervals      = array (
        'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60
    );
    //now we just find the difference
    if ($diff == 0)
    {
        return lang('just_now');
    }
    if ($diff < 60)
    {
        return lang('just_now');
    }
    if ($diff >= 60 && $diff < $intervals['hour'])
    {
        $diff = floor($diff/$intervals['minute']);
        return $diff == 1 ? $diff .' '.lang('minute_ago') : $diff .' '.lang('minutes_ago');
    }
    if ($diff >= $intervals['hour'] && $diff < $intervals['day'])
    {
        $diff = floor($diff/$intervals['hour']);
        return $diff == 1 ? $diff .' '.lang('hour_ago') : $diff .' '.lang('hours_ago');
    }
    if ($diff >= $intervals['day'] && $diff < $intervals['week'])
    {
        $diff = floor($diff/$intervals['day']);
        return $diff == 1 ? $diff .' '.lang('day_ago') : $diff .' '.lang('days_ago');
    }
    if ($diff >= $intervals['week'] && $diff < $intervals['month'])
    {
        $diff = floor($diff/$intervals['week']);
        return $diff == 1 ? $diff .' '.lang('week_ago') : $diff .' '.lang('weeks_ago');
    }
    if ($diff >= $intervals['month'] && $diff < $intervals['year'])
    {
        $diff = floor($diff/$intervals['month']);
        return $diff == 1 ? $diff .' '.lang('month_ago') : $diff .' '.lang('months_ago');
    }
    if ($diff >= $intervals['year'])
    {
        $diff = floor($diff/$intervals['year']);
        return $diff == 1 ? $diff .' '.lang('year_ago') : $diff .' '.lang('years_ago');
    }
}
?>