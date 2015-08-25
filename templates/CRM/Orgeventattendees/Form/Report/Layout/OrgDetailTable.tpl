{* Based on templates/CRM/Report/Form/Layout/Table.tpl
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{if (!$chartEnabled || !$chartSupported )&& $rows}
    {if $pager and $pager->_response and $pager->_response.numPages > 1}
        <div class="report-pager">
            {include file="CRM/common/pager.tpl" location="top"}
        </div>
    {/if}
    <table class="report-layout display">
        {capture assign="tableHeader"}
            {foreach from=$columnHeaders item=header key=field}
                {assign var=class value=""}
                {if $header.type eq 1024 OR $header.type eq 1 OR $header.type eq 512}
                {assign var=class value="class='reports-header-right'"}
                {else}
                    {assign var=class value="class='reports-header'"}
                {/if}
                {if !$skip}
                   {if $header.colspan}
                       <th colspan={$header.colspan}>{$header.title}</th>
                      {assign var=skip value=true}
                      {assign var=skipCount value=`$header.colspan`}
                      {assign var=skipMade  value=1}
                   {else}
                       <th {$class}>{$header.title}</th>
                   {assign var=skip value=false}
                   {/if}
                {else} {* for skip case *}
                   {assign var=skipMade value=`$skipMade+1`}
                   {if $skipMade >= $skipCount}{assign var=skip value=false}{/if}
                {/if}
            {/foreach}
        {/capture}

        {assign var=columnCount value=$columnHeaders|@count}
        {foreach from=$events item=monthevents key=yearmonth}
          <tr class="crm-report-sectionHeader crm-report-sectionHeader-1"><th colspan="{$columnCount}"><h1>{$yearmonth|crmDate}</h1></th></tr>
          {foreach from=$monthevents item=event}
            <tr class="crm-report-sectionHeader crm-report-sectionHeader-2"><th colspan="{$columnCount}"><h2>{$event.title} ({$event.event_type} {$event.start_date|crmDate:'%m/%d/%Y'})</h2></th></tr>
            {assign var=event_id value=$event.id}
            {cycle values="odd-row,even-row" print=0 reset=1 advance=0}
            {if empty($nestedRows.$yearmonth.$event_id)}
              <tr class="{cycle values="odd-row,even-row"} crm-report">
                <td colspan="{$columnCount}">{ts}No participants{/ts}</td>
              </tr>
            {else}
              <tr class="crm-report-sectionCols">{$tableHeader}</tr>
            {/if}
            {foreach from=$nestedRows.$yearmonth.$event_id item=row key=rowid}
           {* {eval var=$sectionHeaderTemplate} *}
              <tr class="{cycle values="odd-row,even-row"} {$row.class} crm-report" id="crm-report_{$yearmonth}_{$event_id}_{$rowid}">
                  {foreach from=$columnHeaders item=header key=field}
                      {assign var=fieldLink value=$field|cat:"_link"}
                      {assign var=fieldHover value=$field|cat:"_hover"}
                      <td class="crm-report-{$field}{if $header.type eq 1024 OR $header.type eq 1 OR $header.type eq 512} report-contents-right{elseif $row.$field eq 'Subtotal'} report-label{/if}">
                          {if $row.$fieldLink}
                              <a title="{$row.$fieldHover}" href="{$row.$fieldLink}">
                          {/if}

                          {if $row.$field eq 'Subtotal'}
                              {$row.$field}
                          {elseif $header.type & 4 OR $header.type & 256}
                              {if $header.group_by eq 'MONTH' or $header.group_by eq 'QUARTER'}
                                  {$row.$field|crmDate:$config->dateformatPartial}
                              {elseif $header.group_by eq 'YEAR'}
                                  {$row.$field|crmDate:$config->dateformatYear}
                              {else}
                                  {if $header.type == 4}
                                     {$row.$field|truncate:10:''|crmDate}
                                  {else}
                                     {$row.$field|crmDate}
                                  {/if}
                              {/if}
                          {elseif $header.type eq 1024}
                              {if $currencyColumn}
                                  <span class="nowrap">{$row.$field|crmMoney:$row.$currencyColumn}</span>
                              {else}
                                  <span class="nowrap">{$row.$field|crmMoney}</span>
                             {/if}
                          {else}
                              {$row.$field}
                          {/if}

                          {if $row.$fieldLink}</a>{/if}
                      </td>
                  {/foreach}
              </tr>
            {/foreach}
          {/foreach}
        {/foreach}

        {if $grandStat}
            {* foreach from=$grandStat item=row*}
            <tr class="total-row">
                {foreach from=$columnHeaders item=header key=field}
                    <td class="report-label">
                        {if $header.type eq 1024}
                            {$grandStat.$field|crmMoney}
                        {else}
                            {$grandStat.$field}
                        {/if}
                    </td>
                {/foreach}
            </tr>
            {* /foreach*}
        {/if}
    </table>
    {if $pager and $pager->_response and $pager->_response.numPages > 1}
        <div class="report-pager">
            {include file="CRM/common/pager.tpl" }
        </div>
    {/if}
{/if}
