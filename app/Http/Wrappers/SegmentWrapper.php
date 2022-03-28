<?php

namespace App\Http\Wrappers;

use App\Models\User;
use Segment\Segment as SegmentClient;

class SegmentWrapper
{
    public static function login(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        if ($user->hasRole(['Agency'])) {
            $properties = 'Agency';
        } else {
            $properties = 'Agency Customer';
        }

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.logged_in'),
            "properties" => array(
                "agency_name" => app('agency')->name,
                "agency_domain" => app('agency')->domain_name,
                "logged_in_as" => $properties
            )
        ));
    }

    public static function registration(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        if ($user->hasRole(['Agency'])) {
            $properties = 'Agency';
        } else {
            $properties = 'Agency Customer';
        }

        SegmentClient::identify(array(
            "userId" => $user->id,
            "traits" => array(
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->username,
            )
        ));

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.registration'),
            "properties" => array(
                "agency_name" => $user->agency()->first()->name,
                "agency_domain" => $user->agency()->first()->domains()->where('default', true)->first()->domain,
                "registered_as" => $properties
            )
        ));
    }
}