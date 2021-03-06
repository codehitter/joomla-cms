<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Tags component
 *
 * @package     Joomla.Site
 * @subpackage  com_tags
 * @since       3.1
 */
class TagsViewTag extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app      = JFactory::getApplication();
		$document = JFactory::getDocument();
		$document->link = JRoute::_(TagsHelperRoute::getTagRoute($app->input->getInt('id')));

		$app->input->set('limit', $app->getCfg('feed_limit'));
		$params = $app->getParams();
		$siteEmail = $app->getCfg('mailfrom');
		$fromName  = $app->getCfg('fromname');
		$feedEmail = $app->getCfg('feed_email', 'author');
		$document->editor = $fromName;
		if ($feedEmail != "none")
		{
			$document->editorEmail = $siteEmail;
		}

		// Get some data from the model
		$items    = $this->get('Items');
		$tag = $this->get('Item');
		foreach ($items as $item)
		{
			// Strip HTML from feed item title
			$title = $this->escape($item->core_title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// URL link to tagged item
			// Change to new routing once it is merged
			$link = JRoute::_($item->link);

			// Strip HTML from feed item description text
			$description = $item->core_body;
			$author			= $item->core_created_by_alias ? $item->core_created_by_alias : $item->author;
			$date = ($item->displayDate ? date('r', strtotime($item->displayDate)) : '');

			// Load individual item creator class
			$feeditem = new JFeedItem;
			$feeditem->title       = $title;
			$feeditem->link        = $link;
			$feeditem->description = $description;
			$feeditem->date        = $date;
			$feeditem->category    = $item->title;
			$feeditem->author      = $author;

			if ($feedEmail == 'site')
			{
				$item->authorEmail = $siteEmail;
			}
			elseif ($feedEmail === 'author')
			{
				$item->authorEmail = $row->author_email;
			}

			// Loads item info into RSS array
			$document->addItem($feeditem);
		}

	}
}
