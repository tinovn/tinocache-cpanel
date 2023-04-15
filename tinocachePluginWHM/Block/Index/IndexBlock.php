<?php
namespace tinocachePlugin\Block\Index;

class IndexBlock
{

    public static function renderPackage($name, $number)
    {
        return '<input type="hidden" name="cfg[' . $number . '][package]" value="' . $name . '">' . $name;
    }

    public static function renderPlan($items, $choosen, $number)
    {

        $options = '<select data-id="' . $number . '" name="cfg[' . $number . '][plan]" class="form-control planChange">';
        $options .= '<option value="NoneMG">Change package...</option>';
        $options .= '<option value="">None</option>';
        foreach ($items as $item):
            $options .= '
                <option value=' . $item->code . ' ' .
                self::isSelected($item->code, $choosen) .
                '>'
                . $item->name .
                '</option>'
            ;
        endforeach;

        $options .= '</select>';

        return $options;
    }

    public static function isSelected($itemA, $itemB)
    {
        foreach (explode(',', $itemB) as $item) {
            if ($itemA === $item) {
                return 'selected="selected"';
            }
        }
    }
}
