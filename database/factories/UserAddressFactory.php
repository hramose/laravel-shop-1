<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserAddress::class, function (Faker $faker) {
    $addresses = [
        ["台北市", "新北市", "桃園市"],
        ["新竹市", "苗栗市", "台中市"],
        ["彰化市", "雲林縣", "嘉義市"],
        ["嘉義縣", "台南市", "台南縣"],
        ["高雄市", "高雄縣", "屏東縣"],
    ];
    $address   = $faker->randomElement($addresses);

    return [
        'province'      => $address[0],
        'city'          => $address[1],
        'district'      => $address[2],
        'address'       => sprintf('第%d街道第%d號', $faker->randomNumber(2), $faker->randomNumber(3)),
        'zip'           => $faker->postcode,
        'contact_name'  => $faker->name,
        'contact_phone' => $faker->phoneNumber,
    ];
});
