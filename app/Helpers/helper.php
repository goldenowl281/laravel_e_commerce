<?php

use App\Models\Category;

    function getCategories()
    {
        return Category::orderBy('name', 'ASC')
                ->with('subCategory')
                ->orderBy('id','DESC')
                ->where('status','1')
                ->where('show_home','Yes')
                ->get();
    }
