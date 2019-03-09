<?php

// 此方法會將當前請求的路由名稱轉換為CSS Class名稱，作用是允許我們針對某個頁面做頁面樣式訂製
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}
