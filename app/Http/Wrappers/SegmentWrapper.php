<?php

namespace App\Http\Wrappers;

use App\Models\User;
use Segment\Segment as SegmentClient;

class SegmentWrapper
{
    public static function login(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.logged_in'),
            "properties" => array(
                "agency_name" => self::getAgencyDetails($user)->agency_name,
                "default_domain" => self::getAgencyDetails($user)->default_domain,
                "custom_domain" => self::getAgencyDetails($user)->custom_domain,
                "logged_in_as" => self::getAgencyDetails($user)->properties
            )
        ));
    }

    public static function registration(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

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
                "agency_name" => self::getAgencyDetails($user)->agency_name,
                "default_domain" => self::getAgencyDetails($user)->default_domain,
                "custom_domain" => self::getAgencyDetails($user)->custom_domain,
                "registered_as" => self::getAgencyDetails($user)->properties
            )
        ));
    }

    public static function userVerification(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.user_verification'),
            "properties" => array(
                "agency_name" => self::getAgencyDetails($user)->agency_name,
                "default_domain" => self::getAgencyDetails($user)->default_domain,
                "custom_domain" => self::getAgencyDetails($user)->custom_domain,
                "verification_of" => self::getAgencyDetails($user)->properties
            )
        ));
    }

    public static function forgotPassword(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.forgot_password'),
            "properties" => array(
                "agency_name" => self::getAgencyDetails($user)->agency_name,
                "default_domain" => self::getAgencyDetails($user)->default_domain,
                "custom_domain" => self::getAgencyDetails($user)->custom_domain,
                "forgot_password_of" => self::getAgencyDetails($user)->properties
            )
        ));
    }

    public static function createPassword(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.create_password'),
            "properties" => array(
                "agency_name" => self::getAgencyDetails($user)->agency_name,
                "default_domain" => self::getAgencyDetails($user)->default_domain,
                "custom_domain" => self::getAgencyDetails($user)->custom_domain,
                "create_password_of" => self::getAgencyDetails($user)->properties
            )
        ));
    }

    public static function generateToken(User $user)
    {
        SegmentClient::init(config('segment.write_key'));

        SegmentClient::track(array(
            "userId" => $user->id,
            "event" => config('agency_events.events.generate_token'),
            "properties" => array(
                "agency_name" => self::getAgencyDetails($user)->agency_name,
                "default_domain" => self::getAgencyDetails($user)->default_domain,
                "custom_domain" => self::getAgencyDetails($user)->custom_domain,
                "generate_token_as" => self::getAgencyDetails($user)->properties
            )
        ));
    }

    public static function getAgencyDetails(User $user)
    {
        if ($user->hasRole(['Agency'])) {
            $properties = 'Agency';
        } else {
            $properties = 'Agency Customer';
        }

        $customDomain = $user->agency()->first()->customDomain();
        $defaultDomain = $user->agency()->first()->defaultDomain();
        $agency = new \stdClass();
        $agency->agency_name = $user->agency()->first()->name;
        $agency->default_domain = empty($defaultDomain) ? "" : $defaultDomain->domain;
        $agency->custom_domain = empty($customDomain) ? "" : $customDomain->domain;
        $agency->properties = $properties;
        return $agency;
    }
}