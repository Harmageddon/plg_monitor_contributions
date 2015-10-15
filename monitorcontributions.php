<?php
/**
 * @package     MonitorContributions
 * @subpackage  content
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

		JLoader::register('MonitorHelper', JPATH_ROOT . '/administrator/components/com_monitor/helper/helper.php');
		JLoader::register('MonitorModelAbstract', JPATH_ROOT . '/administrator/components/com_monitor/model/abstract.php');
		JLoader::register('MonitorModelComment', JPATH_ROOT . '/administrator/components/com_monitor/model/comment.php');
		JLoader::register('MonitorModelIssue', JPATH_ROOT . '/administrator/components/com_monitor/model/issue.php');

		$modelIssue = new MonitorModelIssue(null, false);
		$modelComment = new MonitorModelComment(null, false);

		// Load language files from the component.
		$lang = JFactory::getLanguage();
		$lang->load('com_monitor', JPATH_SITE . '/components/com_monitor');

		$filters = array(
			'author' => $item->user_id,
		);
		$listIssues = array(
			'fullordering' => 'i.created DESC',
			'limit' => $this->params->get('limit_issues', 10),
		);
		$listComments = array(
			'fullordering' => 'c.created DESC',
			'limit' => $this->params->get('limit_comments', 10),
		);
		$displayData = array(
			'item' => $item,
		);
		$displayData['issues'] = $modelIssue->getIssues($filters, $listIssues);
		$displayData['comments'] = $modelComment->getComments($filters, $listComments);
		$displayData['params'] = $this->params->merge(JComponentHelper::getParams('com_monitor'));

		return JLayoutHelper::render('contributions', $displayData, __DIR__ . '/layouts');
	}
}
