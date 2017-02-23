<?php

function calc($money)
{
    $bottles = (int)($money / 2);
    $capsules = 0;
    $empty = 0;


    for ($i = 0; $i <$bottles; $i++) {//每次喝一瓶
        $capsules++;//瓶盖增加一个
        $empty++; //空瓶增加一个

        //看是不是可以用瓶盖换了
        if ($capsules > 0 && $capsules % 4 === 0) {
            $bottles++;//再来一瓶
            $capsules -= 4;//给老板4个瓶盖
        }

        //看是不是可以用空瓶换了
        if ($empty > 0 && $empty % 2 === 0) {
            $bottles++;//再来一瓶
            $empty -= 2;//给老板2个瓶盖
        }
    }

    return ['bottles' => $bottles, 'capsules' => $capsules, 'empty' => $empty];
}

var_dump(calc(10));
