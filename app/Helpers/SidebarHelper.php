<?php
if (!function_exists('buildMenuAdmin')) {
   
    function buildMenuAdmin($elements,$parent=0,$url='admin')
    {
        $result = '';
        if($parent){
            $result .= '<ul class="treeview-menu">';
        }
        foreach ($elements as $element)
        {
            if ($element->parent_id == $parent){
                
                if (menuHasChildren($elements,$element->id)){
                    $result.= '<li id="menu-'.$element->id.'" class="treeview"><a href="'.url($url.'/'.$element->menu_route).'"><i class="'.$element->menu_icon.'"></i> <span>'.$element->menu_name.'</span>';
                    $result.= '<span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                    </span>';
                    $result.='</a>';
                }
                else{
                    $result.= '<li id="menu-'.$element->id.'"><a href="'.url($url.'/'.$element->menu_route).'"><i class="'.($element->menu_icon?$element->menu_icon:'fa fa-circle-o').'"></i> <span>'.$element->menu_name.'</span></a>';
                }
                if (menuHasChildren($elements,$element->id))
                    $result.= buildMenuAdmin($elements,$element->id,$url);
                $result.= "</li>";
            }
        }
        if($parent){
            $result.= "</ul>";
        }
        return $result; 
    }
}

if (!function_exists('menuHasChildren')) {
    function menuHasChildren($rows,$id) {
        foreach ($rows as $row) {
            if ($row->parent_id == $id)
                return true;
        }
        return false;
    }
}