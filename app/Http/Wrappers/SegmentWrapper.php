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
                "default_domain" => app('agency')->domain_name,
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
                "default_domain" => $user->agency()->first()->defaultDomain(),
                "registered_as" => $properties
            )
        ));
    }

    public static function userVerification(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        if ($user->hasRole(['Agency'])) {
            $properties = 'Agency';
        } else {
            $properties = 'Agency Customer';
        }

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.user_verification'),
            "properties" => array(
                "agency_name" => $user->agency()->first()->name,
                "default_domain" => $user->agency()->first()->defaultDomain(),
                "verification_of" => $properties
            )
        ));
    }

    public static function forgotPassword(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        if ($user->hasRole(['Agency'])) {
            $properties = 'Agency';
        } else {
            $properties = 'Agency Customer';
        }

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.forgot_password'),
            "properties" => array(
                "agency_name" => $user->agency()->first()->name,
                "default_domain" => $user->agency()->first()->defaultDomain(),
                "forgot_password_of" => $properties
            )
        ));
    }

    public static function createPassword(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        if ($user->hasRole(['Agency'])) {
            $properties = 'Agency';
        } else {
            $properties = 'Agency Customer';
        }

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.create_password'),
            "properties" => array(
                "agency_name" => $user->agency()->first()->name,
                "default_domain" => $user->agency()->first()->defaultDomain(),
                "create_password_of" => $properties
            )
        ));
    }

    public static function generateToken(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        $properties = 'Agency';

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.generate_token'),
            "properties" => array(
                "agency_name" => $user->agency()->first()->name,
                "default_domain" => $user->agency()->first()->defaultDomain(),
                "generate_token_as" => $properties
            )
        ));
    }
}