<?php

namespace TheStart\Services\Formatting;

class DateFormatter
{
    public function format($post_id)
    {
        $start_date = get_field('start_date', $post_id) ?: null;
        $end_date = get_field('end_date', $post_id) ?: null;
        $start_time = get_field('start_time', $post_id) ?: null;
        $end_time = get_field('end_time', $post_id) ?: null;

        if (!$start_date) {
            return '';
        }

        $now = new \DateTime();
        $start = new \DateTime($start_date);
        $end = $end_date ? new \DateTime($end_date) : null;

        $same_date = $end && $start->format('Y-m-d') === $end->format('Y-m-d');
        $relative_day = $this->getRelativeDay($start, $now);

        // Case 1: Event has ended
        if ($end && $end < $now) {
            return $this->formatEndedEvent($end);
        }

        // Case 2: Ongoing event (started in past, ends in future)
        if ($start < $now && $end && $end > $now) {
            return $this->formatOngoingEvent($end, $end_time);
        }

        // Case 3: Single date or same start/end date
        if (!$end || $same_date) {
            return $this->formatSingleDate($start, $relative_day, $start_time, $end_time, $same_date);
        }

        // Case 4: Date range
        return $this->formatDateRange($start, $end, $now, $relative_day, $start_time, $end_time);
    }

    private function formatTime($time)
    {
        // Try multiple format patterns
        $time_obj = \DateTime::createFromFormat('H:i:s', $time)
            ?: \DateTime::createFromFormat('H:i', $time)
            ?: \DateTime::createFromFormat('h:i a', $time)
            ?: \DateTime::createFromFormat('h:i A', $time);

        if (!$time_obj) {
            return $time;
        }

        $hour = (int)$time_obj->format('g');
        $minute = (int)$time_obj->format('i');
        $ampm = $time_obj->format('a');

        // Format without :00 for whole hours
        if ($minute === 0) {
            return $hour . $ampm;
        }

        return $hour . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . $ampm;
    }

    private function getRelativeDay($date, $now)
    {
        $diff = $now->diff($date);
        $days = (int)$diff->format('%r%a');

        if ($days === 0) return 'Today';
        if ($days === 1) return 'Tomorrow';
        if ($days === -1) return 'Yesterday';
        if ($days > 1 && $days <= 7) return 'This ' . $date->format('l');
        if ($days < -1 && $days >= -7) return 'Last ' . $date->format('l');

        return null;
    }

    private function formatEndedEvent($end)
    {
        return 'Ended ' . $end->format('M j');
    }

    private function formatOngoingEvent($end, $end_time)
    {
        if ($end_time) {
            return 'Now Until ' . $end->format('M j') . ' ' . $this->formatTime($end_time);
        }

        return 'Now Until ' . $end->format('M j');
    }

    private function formatSingleDate($start, $relative_day, $start_time, $end_time, $same_date)
    {
        $date_str = $relative_day ?: $start->format('M j');

        if ($start_time && $end_time && $same_date) {
            return $date_str . ' ' . $this->formatTime($start_time) . ' - ' . $this->formatTime($end_time);
        }

        if ($start_time) {
            return $date_str . ' ' . $this->formatTime($start_time);
        }

        return $date_str;
    }

    private function formatDateRange($start, $end, $now, $relative_day, $start_time, $end_time)
    {
        $start_str = $relative_day ?: $start->format('M j');
        $end_str = $end->format('M j');

        // Add year if crossing year boundary or not current year
        if ($start->format('Y') !== $end->format('Y')) {
            $start_str = $start->format('M j, Y');
            $end_str = $end->format('M j, Y');
        } elseif ($start->format('Y') !== $now->format('Y')) {
            $end_str = $end->format('M j, Y');
        }

        // With times
        if ($start_time && $end_time) {
            if ($start_time === $end_time) {
                return $start_str . '-' . $end->format('j') . ', ' . $this->formatTime($start_time);
            }
            return $start_str . ' ' . $this->formatTime($start_time) . ' - ' . $end_str . ' ' . $this->formatTime($end_time);
        }

        if ($start_time) {
            return $start_str . ' ' . $this->formatTime($start_time) . ' - ' . $end_str;
        }

        return $start_str . ' - ' . $end_str;
    }
}
