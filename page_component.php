<?php
/*
Plugin Name: Page Component
Description: Add components for pages
Version: 0.1
Author: Dmitry Yakovlev
Author URI: http://dimayakovlev.ru/
*/
$thisfile = basename(__FILE__, ".php");

register_plugin(
  $thisfile,
  i18n_r($thisfile.'/TITLE'),
  '0.1',
  i18n_r($thisfile.'/AUTHOR'),
  'http://dimayakovlev.ru',
  i18n_r($thisfile.'/DESCRIPTION'),
  '',
  ''
);

add_action('edit-tab', 'pluginPageComponentGUI');
add_filter('pagesavexml', 'pluginPageComponentSaveData');
add_filter('draftsavexml', 'pluginPageComponentSaveData');

function pluginPageComponentGUI() {
    global $id, $data_edit;
    $component = $component_enable = '';
    if ($id) {
      if ($data_edit) {
        $component = stripslashes($data_edit->pageComponent);
        $component_enable = $data_edit->pageComponentEnable;
      }
    } else {
      $component = isset($_GET['pageComponent']) ? var_in($_GET['pageComponent']) : '';
      $component_enable =  isset($_GET['pageComponentEnable']) ? var_in($_GET['pageComponentEnable']) : '';
    }
    $component_enable = $component_enable == '1' ? 'checked'  : '';
?>
<div id="page_component" class="tab">
  <fieldset>    
    <legend>Component</legend>
    <p class="inline clearfix">
      <input type="checkbox" id="post-pageComponentEnable" name="post-pageComponentEnable" <?php echo $component_enable; ?> />
      <label class="checkbox" for="post-pageComponentEnable">Enable page component</label>
    </p>
    <div class="codewrap">
      <textarea name="post-pageComponent" <?php echo getCodeEditorAttr(''); ?>><?php echo $component; ?></textarea>
    </div>
  </fieldset>
</div>
<?php }

function pluginPageComponentSaveData($xml) {
  $component = isset($_POST['post-pageComponent']) ? safe_slash_html($_POST['post-pageComponent']) : '';
  $component_enable = isset($_POST['post-pageComponentEnable']) ? '1' : '0';
  $xml->addCDataChild('pageComponent', $component);
  $xml->addCDataChild('pageComponentEnable', $component_enable);
  return $xml;
}
/**
 * Check if page component is enabled
 *
 * @return bool
 */
function page_component_enabled() {
  global $data_index;
  return (bool) (string) $data_index->pageComponentEnable == '1';
}

function get_page_component($force = false, $raw = false) {
  global $data_index;
  $component = (string)$data_index->pageComponent;
  if (!$component || !page_component_enabled() && !$force) return;
  if(!$raw) eval("?>" . strip_decode($component) . "<?php ");
	else echo strip_decode($component);
}

/**
 * Return page component
 * Returns page component output 
 *
 * @return string Page component buffered output
 */
function return_page_component() {
  $args = func_get_args();
  return catchOutput('get_page_component', $args);
}