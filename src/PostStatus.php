<?php

namespace AxelSpringer\WP\Mango;

/**
 * Class PostStatus
 *
 */
abstract class PostStatus
{
  const Publish   = 'publish';
  const Future    = 'future';
  const Draft     = 'draft';
  const Pending   = 'pending';
  const Privat    = 'private';
  const Trash     = 'trash';
  const AutoDraft = 'auto-draft';
  const Inherit   = 'inherit';
}
