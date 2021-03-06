<?php

function smarty_function_date_weekday(array $params, Smarty_Internal_Template $template) {
    /** @var CM_Frontend_Render $render */
    $render = $template->smarty->getTemplateVars('render');
    /** @var DateTime $date */
    $date = $params['date'];
    $timeZone = isset($params['timeZone']) ? $params['timeZone'] : null;
    $long = isset($params['long']) ? (bool) $params['long'] : false;
    $pattern = $long ? 'cccc' : 'cccccc';

    $formatter = $render->getFormatterDate(IntlDateFormatter::NONE, IntlDateFormatter::NONE, $pattern, $timeZone);
    $dateString = $formatter->format($date->getTimestamp());
    if (!$long) {
        $dateString = substr($dateString, 0, 2);
    }

    return $dateString;
}
