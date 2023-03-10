<?php

namespace App\Services\Admin;

use App\Models\LiveEvent;
use App\Models\Meeting;
use Illuminate\Support\Carbon;

class ScheduleService
{
    /**
     * @return Meeting|null
     */
    public function nextMeeting(): Meeting|null
    {
        return Meeting::where('start_at', '>=', now())->orderBy('start_at')->first();
    }

    /**
     * @return Carbon|null
     */
    public function nextMeetingDate(): Carbon|null
    {
        $meeting = $this->nextMeeting();

        return $meeting ? $meeting->start_at : null;
    }

    /**
     * @return string|null
     */
    public function nextMeetingDateFormat(string $format = 'Y-m-d H:i'): string|null
    {
        $nextMeetingDate = $this->nextMeetingDate();

        return $nextMeetingDate ? $nextMeetingDate->format($format) : null;
    }

      /**
     * @return LiveEvent|null
     */
    public function nextLiveEvent(): LiveEvent|null
    {
        return LiveEvent::where('start_at', '>=', now())->orderBy('start_at')->first();
    }

    /**
     * @return Carbon|null
     */
    public function nextLiveEventDate(): Carbon|null
    {
        $liveEvent = $this->nextLiveEvent();

        return $liveEvent ? $liveEvent->start_at : null;
    }

    /**
     * @return string|null
     */
    public function nextLiveEventDateFormat(string $format = 'Y-m-d H:i'): string|null
    {
        $nextLiveEventDate = $this->nextLiveEventDate();

        return $nextLiveEventDate ? $nextLiveEventDate->format($format) : null;
    }

    /**
     * @return LiveEvent|Meeting|null
     */
    public function nextEvent(): LiveEvent|Meeting|null
    {
        $event = null;

        $nextLiveEventDate = $this->nextLiveEventDate();

        $nextMeetingDate = $this->nextMeetingDate();

        if ($nextLiveEventDate && $nextMeetingDate) {
            $event = $nextLiveEventDate->lte($nextMeetingDate) ? $this->nextLiveEvent() :  $this->nextMeeting();
        } elseif($nextLiveEventDate){
            $event = $this->nextLiveEvent();
        } elseif($nextMeetingDate){
            $event = $this->nextMeeting();
        }

        return $event;
    }

    /**
     * @return Carbon|null
     */
    public function nextEventDate(): Carbon|null
    {
        $eventDate = null;

        $nextLiveEventDate = $this->nextLiveEventDate();

        $nextMeetingDate = $this->nextMeetingDate();

        if ($nextLiveEventDate && $nextMeetingDate) {
            $eventDate = $nextLiveEventDate->lte($nextMeetingDate) ? $nextLiveEventDate :  $nextMeetingDate;
        } elseif($nextLiveEventDate){
            $eventDate = $nextLiveEventDate;
        } elseif($nextMeetingDate){
            $eventDate = $nextMeetingDate;
        }

        return $eventDate;
    }

    /**
     * @return string|null
     */
    public function nextEventDateFormat(string $format = 'Y-m-d H:i'): string|null
    {
        $eventDate = $this->nextEventDate();

        return $eventDate ? $eventDate->format($format) : null;
    }
}
