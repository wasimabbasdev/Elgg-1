<?php
/**
 * Class for discussion reply
 *
 * We extend ElggComment to get the future thread support.
 */
class ElggDiscussionReply extends ElggComment {

	/**
	 * {@inheritDoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = "discussion_reply";
	}
}
