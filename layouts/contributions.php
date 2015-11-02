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
 * @var  \Joomla\Registry\Registry  $displayData
 */
$params     = $displayData['params'];
$item       = $displayData['item'];
$dateFormat = $params->get('issue_date_format', JText::_('DATE_FORMAT_LC2'));
?>

<h3>
	<?php echo JText::_('COM_MONITOR_ISSUES'); ?>
</h3>
<?php if (empty($displayData['issues'])) : ?>
	<div class="contact-issues no-issues muted">
		<?php echo JText::_('PLG_CONTENT_MONITORCONTRIBUTIONS_NO_ISSUES'); ?>
	</div>
<?php else : ?>
	<table class="contact-issues table table-striped table-hover">
		<thead>
		<tr>
			<th><?php echo JText::_('COM_MONITOR_ISSUE_TITLE'); ?></th>
			<th><?php echo JText::_('COM_MONITOR_STATUS'); ?></th>
			<th><?php echo JText::_('COM_MONITOR_CREATE_DATE'); ?></th>
			<th><?php echo JText::_('COM_MONITOR_PROJECT_NAME'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($displayData['issues'] as $issue) : ?>
			<tr>
				<td>
					<?php
					echo JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=issue&id=' . $issue->id), $issue->title);
					?>
				</td>
				<td>
					<?php
					$statusHelp = ($params->get('show_status_help', 1) && isset($issue->status_help))
						? 'data-content="' . $issue->status_help . '" data-original-title="' . $issue->status . '"'
						: '';
					?>
					<span class="<?php echo $issue->status_style; ?> hasPopover"
						<?php echo $statusHelp; ?>>
				<?php echo $issue->status; ?>
			</span>
				</td>
				<td>
					<?php echo JHtml::_('date', $issue->created, $dateFormat); ?>
				</td>
				<td>
					<?php
					$view = $params->get('project_link_to', '');

					if ($view === 'project')
					{
						echo JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=project&id=' . $issue->project_id), $issue->project_name);
					}
					elseif ($view === 'issues')
					{
						echo JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=issues&project_id=' . $issue->project_id), $issue->project_name);
					}
					else
					{
						echo $issue->project_name;
					}
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	if ($params->get('link_more_issues', 1))
	{
		$title = JText::sprintf('PLG_CONTENT_MONITORCONTRIBUTIONS_MORE_ISSUES_BY', $item->name);
		echo JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=issues&filter[author]=' . $item->user_id), $title);
	}
	?>

<?php endif; ?>

<h3>
	<?php echo JText::_('COM_MONITOR_COMMENTS'); ?>
</h3>
<?php if (empty($displayData['comments'])) : ?>
	<div class="contact-comments no-comments muted">
		<?php echo JText::_('PLG_CONTENT_MONITORCONTRIBUTIONS_NO_COMMENTS'); ?>
	</div>
<?php else : ?>
<table class="contact-comments table table-striped table-hover">
	<thead>
	<tr>
		<th><?php echo JText::_('COM_MONITOR_ISSUE'); ?></th>
		<th><?php echo JText::_('COM_MONITOR_COMMENT_TEXT'); ?></th>
		<th><?php echo JText::_('COM_MONITOR_CREATE_DATE'); ?></th>
		<th><?php echo JText::_('COM_MONITOR_PROJECT_NAME'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($displayData['comments'] as $comment) : ?>
		<tr>
			<td>
				<?php
				$link = 'index.php?option=com_monitor&view=issue&id=' . $comment->issue_id . '#comment-' . $comment->id;
				echo JHtml::_('link', JRoute::_($link), $comment->issue_title);
				?>
			</td>
			<td>
				<?php echo $this->escape(MonitorHelper::cutStr($comment->text, $params->get('comment_text_length', 100))); ?>
			</td>
			<td>
				<?php echo JHtml::_('date', $comment->created, $dateFormat); ?>
			</td>
			<td>
				<?php
				$view = $params->get('project_link_to', '');

				if ($view === 'project')
				{
					echo JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=project&id=' . $comment->project_id), $comment->project_name);
				}
				elseif ($view === 'issues')
				{
					echo JHtml::_('link', JRoute::_('index.php?option=com_monitor&view=issues&project_id=' . $comment->project_id), $comment->project_name);
				}
				else
				{
					echo $comment->project_name;
				}
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
if ($params->get('link_more_comments', 1))
{
	$title = JText::sprintf('PLG_CONTENT_MONITORCONTRIBUTIONS_MORE_COMMENTS_BY', $item->name);
	echo $title;

	// TODO
}
?>
<?php endif; ?>
