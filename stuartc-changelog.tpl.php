<div id="changelog">
  <div class="content">
    <div class="title">
      <?php print theme('textimage_image', 'page_title', 'Changelog', array(), 'png', $title, $title); ?>
    </div>
    <div class="changelog">
      <?php foreach ($changelog as $build => $versions) : ?>
      <div class="build build-<?php print $build; ?>">
        <?php foreach ($versions as $version => $data) : ?>
        <div class="version vesion-<?php print $build .'-'. $version; ?>">
          <a name="<?php print $node->field_project_id[0]['value'] .'-'. $build .'-'. $version; ?>"></a>
          <?php print theme('textimage_image', 'section_title_grey', $data['title'], array(), 'png', $data['title'], $data['title']); ?>
          <div class="entries">
            <?php print $data['data']; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
