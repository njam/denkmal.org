<?php

class Denkmal_Scraper_Source_Programmzeitung extends Denkmal_Scraper_Source_Abstract {

    public function run() {
        return Functional\flatten(Functional\map($this->_manager->getDateList(), function (DateTime $date) {
            $dateStr = $date->format('d.m.Y');
            $url = 'http://programmzeitung.programmonline.ch/Content/Tagesagenda?startDate=' . $dateStr;
            $content = self::loadUrl($url, 5);

            return $this->processPageDate($content, $date);
        }));
    }

    /**
     * @param string   $html
     * @param DateTime $date
     * @throws CM_Exception_Invalid
     * @return Denkmal_Scraper_EventData[]
     */
    public function processPageDate($html, DateTime $date) {
        $html = new CM_Dom_NodeList($html, true);

        /** @var CM_Dom_NodeList[] $agendaTableList */
        $agendaTableList = [];
        $agendaTableTitle = null;
        foreach ($html->find('.tagesagenda > *') as $i => $agendaChild) {
            if (0 === strpos($agendaChild->getHtml(), '<h2')) {
                $agendaTableTitle = $agendaChild->getText();
            }
            if (0 === strpos($agendaChild->getHtml(), '<table') && null !== $agendaTableTitle) {
                $agendaTableList[$agendaTableTitle] = $agendaChild;
            }
        }

        if (empty($agendaTableList)) {
            throw new CM_Exception_Invalid('Cannot detect event tables', ['html' => $html->getHtml()]);
        }

        if (!isset($agendaTableList['Sounds & Floors'])) {
            return [];
        }
        $agendaTable = $agendaTableList['Sounds & Floors'];

        return Functional\map($agendaTable->find('tr'), function (CM_Dom_NodeList $agendaTableRow) use ($date) {
            if (3 != count($agendaTableRow->find('td'))) {
                throw new CM_Exception_Invalid('Unexpected row count.', ['html' => $agendaTableRow->getHtml()]);
            }
            $timeNode = $agendaTableRow->find('td:eq(0)');
            $descriptionNode = $agendaTableRow->find('td:eq(1)');
            $venueNode = $agendaTableRow->find('td:eq(2)');

            $timeTextList = explode('<br>', $timeNode->getChildren()->getHtml());
            $timeText = $timeTextList[0];
            if (!preg_match('#^(\d+):(\d+)(\s+.\s+(\d+):(\d+))?$#u', $timeText, $matches)) {
                throw new CM_Exception_Invalid('Cannot detect time from `' . $timeText . '`.');
            }
            $from = new Denkmal_Scraper_Date($date);
            $from->setTime($matches[1], $matches[2]);
            $until = null;
            if (isset($matches[4]) && isset($matches[5])) {
                $until = clone $from;
                $until->setTime($matches[4], $matches[5]);
            }

            $description = new Denkmal_Scraper_Description(
                $this->_cleanupText($descriptionNode->getChildren(XML_TEXT_NODE)->getText()),
                $this->_cleanupText($descriptionNode->find('b')->getText())
            );

            $venueText = $venueNode->getText();
            $venueText = preg_replace('#,.*?$#u', '', $venueText);
            $venueText = preg_replace('#\[.+?\].*$#u', '', $venueText);
            $venueText = preg_replace('#♦.*$#u', '', $venueText);
            $venueText = trim($venueText);

            return new Denkmal_Scraper_EventData($venueText, $description, $from, $until);
        });
    }

    /**
     * @param string $text
     * @return string
     */
    private function _cleanupText($text) {
        $text = str_replace('♦', '-', $text);
        return $text;
    }
}
