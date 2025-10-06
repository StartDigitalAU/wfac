<?php

namespace TheStart\Services\Formatting;

class OpeningHoursFormatter
{
    private $opening_hours_string = '';
    private $is_open = false;
    private $status_message = '';

    public function __construct()
    {
        $this->get_opening_hours_field();
        $this->is_open();
    }

    private function get_opening_hours_field()
    {
        if (function_exists('get_fields')) {
            $options = get_fields('options');

            if ($options && isset($options['company_details']['opening_hours'])) {
                $this->opening_hours_string = $options['company_details']['opening_hours'];
            } else {
                $this->opening_hours_string = '';
            }
        } else {
            $this->opening_hours_string = '';
        }
    }

    private function is_open()
    {
        // Check if we have opening hours data
        if (empty($this->opening_hours_string)) {
            $this->is_open = false;
            $this->status_message = 'Hours not available';
            return;
        }

        // Set Perth timezone
        $perth_tz = new \DateTimeZone('Australia/Perth');
        $now = new \DateTime('now', $perth_tz);

        // Parse the opening hours string (e.g., "9am - 5pm")
        $hours = $this->parse_hours($this->opening_hours_string);

        if (!$hours) {
            $this->is_open = false;
            $this->status_message = 'Hours not available';
            return;
        }

        $current_time = $now->format('H:i');

        // Check if current time is within opening hours
        if ($current_time >= $hours['open'] && $current_time < $hours['close']) {
            $this->is_open = true;
            $this->status_message = 'Open now until ' . $hours['close_display'];
        } else {
            $this->is_open = false;
            $this->status_message = 'Closed';
        }
    }

    private function parse_hours($hours_string)
    {
        // Match patterns like "9am - 5pm" or "9:00am - 5:00pm"
        preg_match(
            '/(\d{1,2}):?(\d{2})?\s*(am|pm)\s*-\s*(\d{1,2}):?(\d{2})?\s*(am|pm)/i',
            $hours_string,
            $matches
        );

        if (!$matches) {
            return false;
        }

        // Parse opening time
        $open_hour = (int)$matches[1];
        $open_min = isset($matches[2]) && $matches[2] !== '' ? $matches[2] : '00';
        $open_period = strtolower($matches[3]);

        // Parse closing time
        $close_hour = (int)$matches[4];
        $close_min = isset($matches[5]) && $matches[5] !== '' ? $matches[5] : '00';
        $close_period = strtolower($matches[6]);

        // Convert to 24-hour format
        if ($open_period === 'pm' && $open_hour !== 12) {
            $open_hour += 12;
        } elseif ($open_period === 'am' && $open_hour === 12) {
            $open_hour = 0;
        }

        if ($close_period === 'pm' && $close_hour !== 12) {
            $close_hour += 12;
        } elseif ($close_period === 'am' && $close_hour === 12) {
            $close_hour = 0;
        }

        return [
            'open' => sprintf('%02d:%02d', $open_hour, $open_min),
            'close' => sprintf('%02d:%02d', $close_hour, $close_min),
            'close_display' => $matches[4] . ($matches[5] ? ':' . $matches[5] : '') . $matches[6]
        ];
    }

    public function get_status()
    {
        return $this->status_message;
    }

    public function is_currently_open()
    {
        return $this->is_open;
    }
}
