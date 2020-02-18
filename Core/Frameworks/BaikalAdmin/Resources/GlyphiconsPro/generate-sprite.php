#!/usr/bin/env php
<?php

define("COLUMNS", 10);
define("ROWS", 35);
define("MATRIXWIDTH", 400);
define("MATRIXHEIGHT", 1400);

echo generateSprite(getSymbols(), COLUMNS, ROWS, MATRIXWIDTH, MATRIXHEIGHT, "glyph-");

function getSymbols() {
    # Glyphicons Png names, without extension
    return [
        "000_glass",
        "001_leaf",
        "002_dog",
        "003_user",
        "004_girl",
        "005_car",
        "006_user_add",
        "007_user_remove",
        "008_film",
        "009_magic",
        "010_envelope",
        "011_camera",
        "012_heart",
        "013_beach_umbrella",
        "014_train",
        "015_print",
        "016_bin",
        "017_music",
        "018_note",
        "019_cogwheel",
        "020_home",
        "021_snowflake",
        "022_fire",
        "023_cogwheels",
        "024_parents",
        "025_binoculars",
        "026_road",
        "027_search",
        "028_cars",
        "029_notes_2",
        "030_pencil",
        "031_bus",
        "032_wifi_alt",
        "033_luggage",
        "034_old_man",
        "035_woman",
        "036_file",
        "037_credit",
        "038_airplane",
        "039_notes",
        "040_stats",
        "041_charts",
        "042_pie_chart",
        "043_group",
        "044_keys",
        "045_calendar",
        "046_router",
        "047_camera_small",
        "048_dislikes",
        "049_star",
        "050_link",
        "051_eye_open",
        "052_eye_close",
        "053_alarm",
        "054_clock",
        "055_stopwatch",
        "056_projector",
        "057_history",
        "058_truck",
        "059_cargo",
        "060_compass",
        "061_keynote",
        "062_attach",
        "063_power",
        "064_lightbulb",
        "065_tag",
        "066_tags",
        "067_cleaning",
        "068_ruller",
        "069_gift",
        "070_umbrella",
        "071_book",
        "072_bookmark",
        "073_signal",
        "074_cup",
        "075_stroller",
        "076_headphones",
        "077_headset",
        "078_warning_sign",
        "079_signal",
        "080_retweet",
        "081_refresh",
        "082_roundabout",
        "083_random",
        "084_heat",
        "085_repeat",
        "086_display",
        "087_log_book",
        "088_adress_book",
        "089_magnet",
        "090_table",
        "091_adjust",
        "092_tint",
        "093_crop",
        "094_vector_path_square",
        "095_vector_path_circle",
        "096_vector_path_polygon",
        "097_vector_path_line",
        "098_vector_path_curve",
        "099_vector_path_all",
        "100_font",
        "101_italic",
        "102_bold",
        "103_text_underline",
        "104_text_strike",
        "105_text_height",
        "106_text_width",
        "107_text_resize",
        "108_left_indent",
        "109_right_indent",
        "110_align_left",
        "111_align_center",
        "112_align_right",
        "113_justify",
        "114_list",
        "115_text_smaller",
        "116_text_bigger",
        "117_embed",
        "118_embed_close",
        "119_adjust",
        "120_message_full",
        "121_message_empty",
        "122_message_in",
        "123_message_out",
        "124_message_plus",
        "125_message_minus",
        "126_message_ban",
        "127_message_flag",
        "128_message_lock",
        "129_message_new",
        "130_inbox",
        "131_inbox_plus",
        "132_inbox_minus",
        "133_inbox_lock",
        "134_inbox_in",
        "135_inbox_out",
        "136_computer_locked",
        "137_computer_service",
        "138_computer_proces",
        "139_phone",
        "140_database_lock",
        "141_database_plus",
        "142_database_minus",
        "143_database_ban",
        "144_folder_open",
        "145_folder_plus",
        "146_folder_minus",
        "147_folder_lock",
        "148_folder_flag",
        "149_folder_new",
        "150_check",
        "151_edit",
        "152_new_window",
        "153_more_windows",
        "154_show_big_thumbnails",
        "155_show_thumbnails",
        "156_show_thumbnails_with_lines",
        "157_show_lines",
        "158_playlist",
        "159_picture",
        "160_imac",
        "161_macbook",
        "162_ipad",
        "163_iphone",
        "164_iphone_transfer",
        "165_iphone_exchange",
        "166_ipod",
        "167_ipod_shuffle",
        "168_ear_plugs",
        "169_albums",
        "170_step_backward",
        "171_fast_backward",
        "172_rewind",
        "173_play",
        "174_pause",
        "175_stop",
        "176_forward",
        "177_fast_forward",
        "178_step_forward",
        "179_eject",
        "180_facetime_video",
        "181_download_alt",
        "182_mute",
        "183_volume_down",
        "184_volume_up",
        "185_screenshot",
        "186_move",
        "187_more",
        "188_brightness_reduce",
        "189_brightness_increase",
        "190_circle_plus",
        "191_circle_minus",
        "192_circle_remove",
        "193_circle_ok",
        "194_circle_question_mark",
        "195_circle_info",
        "196_circle_exclamation_mark",
        "197_remove",
        "198_ok",
        "199_ban",
        "200_download",
        "201_upload",
        "202_shopping_cart",
        "203_lock",
        "204_unlock",
        "205_electricity",
        "206_ok_2",
        "207_remove_2",
        "208_cart_out",
        "209_cart_in",
        "210_left_arrow",
        "211_right_arrow",
        "212_down_arrow",
        "213_up_arrow",
        "214_resize_small",
        "215_resize_full",
        "216_circle_arrow_left",
        "217_circle_arrow_right",
        "218_circle_arrow_right",
        "219_circle_arrow_right",
        "220_play_button",
        "221_unshare",
        "222_share",
        "223_thin_right_arrow",
        "224_thin_arrow_left",
        "225_bluetooth",
        "226_euro",
        "227_usd",
        "228_bp",
        "229_retweet_2",
        "230_moon",
        "231_sun",
        "232_cloud",
        "233_direction",
        "234_brush",
        "235_pen",
        "236_zoom_in",
        "237_zoom_out",
        "238_pin",
        "239_riflescope",
        "240_rotation_lock",
        "241_flash",
        "242_google_maps",
        "243_anchor",
        "244_conversation",
        "245_chat",
        "246_male",
        "247_female",
        "248_asterisk",
        "249_divide",
        "250_snorkel_diving",
        "251_scuba_diving",
        "252_oxygen_bottle",
        "253_fins",
        "254_fishes",
        "255_boat",
        "256_delete_point",
        "257_sheriffs_-star",
        "258_qrcode",
        "259_barcode",
        "260_pool",
        "261_buoy",
        "262_spade",
        "263_bank",
        "264_vcard",
        "265_electrical_plug",
        "266_flag",
        "267_credit_card",
        "268_keyboard_wireless",
        "269_keyboard_wired",
        "270_shield",
        "271_ring",
        "272_cake",
        "273_drink",
        "274_beer",
        "275_fast_food",
        "276_cutlery",
        "277_pizza",
        "278_birthday_cake",
        "279_tablet",
        "280_settings",
        "281_bullets",
        "282_cardio",
        "283_t-shirt",
        "284_pants",
        "285_sweater",
        "286_fabric",
        "287_leather",
        "288_scissors",
        "289_podium",
        "290_skull",
        "291_celebration",
        "292_tea_kettle",
        "293_french_press",
        "294_coffe_cup",
        "295_pot",
        "296_grater",
        "297_kettle",
        "298_hospital",
        "299_hospital_h",
        "300_microphone",
        "301_webcam",
        "302_temple_christianity_church",
        "303_temple_islam",
        "304_temple_hindu",
        "305_temple_buddhist",
        "306_electrical_socket_eu",
        "307_electrical_socket_us",
        "308_bomb",
        "309_comments",
        "310_flower",
        "311_baseball",
        "312_rugby",
        "313_ax",
        "314_table_tennis",
        "315_bowling",
        "316_tree_conifer",
        "317_tree_deciduous",
        "318_more-items",
        "319_sort",
        "320_facebook",
        "321_twitter_t",
        "322_twitter",
        "323_buzz",
        "324_vimeo",
        "325_flickr",
        "326_last_fm",
        "327_rss",
        "328_skype",
        "329_e-mail",
        "330_instapaper",
        "331_evernote",
        "332_xing",
        "333_zootool",
        "334_dribbble",
        "335_deviantart",
        "336_read_it_later",
        "337_linked_in",
        "338_forrst",
        "339_pinboard",
        "340_behance",
        "341_github",
        "342_youtube",
        "343_skitch",
        "344_4square",
        "345_quora",
        "346_google_plus",
        "347_spootify",
        "348_stumbleupon",
        "349_readability",
    ];
}

function generateSprite($aSymbols, $iCols, $iRows, $iPngWidth, $iPngHeight, $sClassPrefix) {
    $iKey = 0;

    $aSprites = [];
    $iSymbolWidth = $iPngWidth / $iCols;
    $iSymbolHeight = $iPngHeight / $iRows;

    foreach ($aSymbols as $sSymbol) {
        $aParts = explode("_", strtolower($sSymbol));
        array_shift($aParts);
        $sClass = $sClassPrefix . implode("-", $aParts);

        $iRowNum = intval($iKey / $iCols);
        $iColNum = $iKey % $iCols;

        $iX = $iColNum * $iSymbolWidth;
        $iY = $iRowNum * $iSymbolHeight;

        $aSprites[] = [
            "class"  => $sClass,
            "x"      => round($iX),
            "y"      => round($iY),
            "width"  => ceil($iSymbolWidth),
            "height" => ceil($iSymbolHeight)
        ];

        ++$iKey;
    }

    ##########################################################################
    # Generate CSS

    $iSpriteWidth = ceil($iSymbolWidth);
    $iSpriteHeight = ceil($iSymbolHeight);

    $sCss = <<<CSS
.btn-large [class^="{$sClassPrefix}"] {
  margin-top: 1px;
}

.btn-small [class^="{$sClassPrefix}"] {
  margin-top: -1px;
}

.nav-list [class^="{$sClassPrefix}"] {
  margin-right: 2px;
}

[class^="{$sClassPrefix}"],
[class*=" {$sClassPrefix}"] {
  display: inline-block;
  width: {$iSpriteWidth}px;
  height: {$iSpriteHeight}px;
  line-height: {$iSpriteHeight}px;
  vertical-align: text-top;
  background-image: url("{$sClassPrefix}dark.png");
  background-position: {$iSpriteWidth}px {$iSpriteHeight}px;
  background-repeat: no-repeat;
  *margin-right: .3em;
}
[class^="{$sClassPrefix}"]:last-child,
[class*=" {$sClassPrefix}"]:last-child {
  *margin-left: 0;
}

.{$sClassPrefix}white {
  background-image: url("{$sClassPrefix}white.png");
}

CSS;

    reset($aSprites);
    foreach ($aSprites as $iKey => $aSprite) {
        $iX = (-1 * intval($aSprite["x"]));
        $iY = (-1 * intval($aSprite["y"]));

        if ($iX < 0) {
            $iX .= "px";
        }

        if ($iY < 0) {
            $iY .= "px";
        }

        $sCss .= <<<CSS
.{$aSprite["class"]} {
	background-position: {$iX} {$iY};
}

CSS;
    }

    $sCss = "\n" . "/* " . count($aSprites) . " glyphs, generated on " . strftime("%Y-%m-%d %H:%M:%S") . "; C=" . $iCols . "; R=" . $iRows . "; W=" . $iPngWidth . "; H=" . $iPngHeight . "; PREFIX=" . $sClassPrefix . " */\n" . $sCss;

    return $sCss;
}
