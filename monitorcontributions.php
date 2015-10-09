<?php
/**
 * @package     MonitorContributions
 * @subpackage  user
 *
 * @copyright   Copyright (C) 2015 Constantin Romankiewicz.
 * @license     Apache License 2.0; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Plugin to show user contributions (issues, comments) in their profiles.
 *
 * @author  Constantin Romankiewicz <constantin@zweiiconkram.de>
 * @since   1.0
 */
class PlgContentMonitorContributions extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Database object to use in the plugin.
	 *
	 * @var JDatabaseDriver
	 *
	 * @since 1.0
	 */
	protected $db;

	/**
	 * Event called after display of content.
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   mixed    &$item    The item displayed.
	 * @param   mixed    $params   Additional parameters.
	 * @param   integer  $page     Optional page number.
	 *
	 * @return  string  Returned value from this event will be displayed after the content of the item.
	 */
	public function onContentAfterDisplay($context, &$item, $params, $page = 0)
	{
		$allowed_contexts = array('com_contact.contact');

		if (!in_array($context, $allowed_contexts))
		{
			return null;
		}

		// Check if the component is installed and enabled.
		if (!JComponentHelper::isEnabled('com_monitor'))
		{
			return null;
		}

		// Load language files from the component.
		$lang = JFactory::getLanguage();
		$lang->load('com_monitor', JPATH_SITE . '/components/com_monitor');

		// Get date format parameter from com_monitor.
		$dateFormat = JComponentHelper::getParams('com_monitor')->get('issue_date_format', JText::_('DATE_FORMAT_LC2'));

		// "Issues" block.
		$html = '<h3>' . JText::_('COM_MONITOR_ISSUES') . '</h3>'
			. '<div class="contact-issues">'
			. ' <ul>';
		$issues = $this->getIssues($item->user_id);

		if (empty($issues))
		{
			$html .= '<li>' . JText::_('PLG_MONITORCONTRIBUTIONS_NO_ISSUES') . '</li>';
		}
		else
		{
			foreach ($issues as $issue)
			{
				$html .= '<li>';
				$title = htmlspecialchars($issue->title, ENT_COMPAT, 'UTF-8');
				$html .= JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=issue&id=' . $issue->id), $title);
				$html .= JHtml::_('date', $issue->created, $dateFormat);
				$html .= '</li>';
			}
		}

		$html .= '</ul>'
			. '</div>';

		// "Comments" block.
		$html .= '<h3>' . JText::_('COM_MONITOR_COMMENTS') . '</h3>'
			. '<div class="contact-comments">'
			. ' <ul>';
		$comments = $this->getComments($item->user_id);

		if (empty($comments))
		{
			$html .= '<li>' . JText::_('PLG_MONITORCONTRIBUTIONS_NO_COMMENTS') . '</li>';
		}
		else
		{
			foreach ($comments as $comment)
			{
				$html .= '<li>';
				$text = htmlspecialchars($comment->text, ENT_COMPAT, 'UTF-8');
				$html .= JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=issue&id=' . $comment->issue_id), $text);
				$html .= JHtml::_('date', $comment->created, $dateFormat);
				$html .= '</li>';
			}
		}

		$html .= '</ul>'
			. '</div>';

		return $html;
	}

	/**
	 * Loads a list of issues by the given author.
	 *
	 * @param   int  $author  User ID of the author.
	 *
	 * @return  mixed   A list of objects representing the issues or null if the query failed.
	 */
	private function getIssues($author)
	{
		$user = JFactory::getUser();

		$query = $this->db->getQuery(true);
		$query->select('i.id, i.title, i.created')
			->from('#__monitor_issues AS i')
			->where('i.author_id = ' . $query->q((int) $author))
			->order('i.created DESC')
			->leftJoin('#__monitor_issue_classifications AS cl ON i.classification = cl.id')
			->where('cl.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

		$this->db->setQuery($query);

		return $this->db->loadObjectList();
	}

	/**
	 * Loads a list of comments by the given author.
	 *
	 * @param   int  $author  User ID of the author.
	 *
	 * @return  mixed   A list of objects representing the comments or null if the query failed.
	 */
	private function getComments($author)
	{
		$user = JFactory::getUser();

		$query = $this->db->getQuery(true);
		$query->select('c.id, c.text, c.created, c.issue_id, i.title AS issue_title')
			->from('#__monitor_comments AS c')
			->where('c.author_id = ' . $query->q((int) $author))
			->order('c.created DESC')
			->leftJoin('#__monitor_issues AS i ON c.issue_id = i.id')
			->leftJoin('#__monitor_issue_classifications AS cl ON i.classification = cl.id')
			->where('cl.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

		$this->db->setQuery($query);

		return $this->db->loadObjectList();
	}
}
