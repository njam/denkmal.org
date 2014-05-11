<p>
  {translate 'Es wurde ein neuer Event für "{$venue}" eingetragen:' venue=$venue->getName()|escape}
</p><p>
  {date_weekday date=$event->getFrom()} {date time=$event->getFrom()->getTimestamp()}
  {date_time date=$event->getFrom()}{if $event->getUntil()} - {date_time date=$event->getUntil()}{/if}
  <br />
  <a href="{linkUrl page=Admin_Page_Venue venue=$venue->getId()}">{$event->getDescription()|escape}</a>
</p>
