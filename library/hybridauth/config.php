<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

return
  [
    "base_url"   => /*"http://105.in.ua/default/index/hybridauth-endpoint"*/
      '',
    "providers"  => [
      // openid providers
      "OpenID"     => [
        "enabled" => true
      ],
      "Yahoo"      => [
        "enabled" => true,
        "keys"    => ["id" => "", "secret" => ""],
      ],
      "AOL"        => [
        "enabled" => true
      ],
      "Google"     => [
        "enabled"         => true,
        "keys"            => [
//                    "id" => "281278630137-1ke3sscu4h80074f50s4h5u5hha4nr02.apps.googleusercontent.com",
//                    "secret" => "ATsi4e2TY2QLjYj8AqK9o2aY"
"id"     => "657207116225-d946qm85hlu3r53br85ltugkogq0hlvs.apps.googleusercontent.com",
"secret" => "xQWOC2Ryig3jvwD2d26aEXOb"
        ],
        "scope"           => "https://www.googleapis.com/auth/userinfo.profile " . // optional
          "https://www.googleapis.com/auth/userinfo.email", // optional
        "access_type"     => "offline", // optional
        "approval_prompt" => "force", // optional

      ],
      "Facebook"   => [
        "enabled" => true,
        "keys"    => ["id" => "692523620858405", "secret" => "9b6e5f7ee82df5340a236747fda7b0fb"],
        /*                "keys" => array("id" => "401720609983591", "secret" => "6f8338b23fb7c63ce616596050bf566b"),*/
        "scope"   => "email", // optional
        "display" => "popup"
      ],
      "Twitter"    => [
        "enabled" => true,
        "keys"    => [
          "key"    => "BbG7ya2tYSImMs5Gav61LCH5v",
          "secret" => "0EBw6AECkUp74FCDue1JNPteg4FG9eQFCCj5Rvrp7Mud4m6S4F"
        ]
      ],
      "Vkontakte"  => [
        "enabled" => true,
        "keys"    => [
          "id"    => "5009682",
          "secret" => "clalqi4g7w0ph856H4zR"
        ]
      ],
      // windows live
      "Live"       => [
        "enabled" => true,
        "keys"    => ["id" => "", "secret" => ""]
      ],
      "MySpace"    => [
        "enabled" => true,
        "keys"    => ["key" => "", "secret" => ""]
      ],
      "LinkedIn"   => [
        "enabled" => true,
        "keys"    => ["key" => "75lwwvon16jl9r", "secret" => "oklKucWp0ZX7qyJM"]
      ],
      "Foursquare" => [
        "enabled" => true,
        "keys"    => ["id" => "", "secret" => ""]
      ],
    ],
    // if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
    "debug_mode" => true,
    "debug_file" => __DIR__ . "/debug.txt",
  ];
