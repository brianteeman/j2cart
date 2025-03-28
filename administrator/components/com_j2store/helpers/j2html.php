<?php
/**
 * @package     Joomla.Component
 * @subpackage  J2Store
 *
 * @copyright Copyright (C) 2014-24 Ramesh Elamathi / J2Store.org
 * @copyright Copyright (C) 2025 J2Commerce, LLC. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\UserField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\Helpers\Select;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserFactory;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;

/**
 * J2Html class provides Form Inputs
 */
class J2Html
{
    public static function label($text, $name = '', $options = [])
    {
        $options['class'] = isset($options['label_class']) ? $options['label_class'] : (isset($options['class']) ? $options['class'] : "");
        $options['for'] = isset($options['for']) ? $options['for'] : $name;
        $attribs = J2Store::platform()->toString($options);
        $html = '<label ' . $attribs . '>' . $text . '</label>';
        return $html;
    }

    /**
     * Create a text input field.
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
	public static function text($name, $value = null, $options = [])
	{
		// Check for 'integer' type and sanitize $value if needed
		$fieldType = $options['field_type'] ?? '';
		if ($fieldType === 'integer') {
			$value = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|', '+', '-'], '', $value);
		}

		return self::input('text', $name, $value, $options);
	}

	/**
     * Create a price input field.
     *
     * @param string $name
     * @param string $currency symbol
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function price($name, $value = null, $options = [])
    {
        $optionvalue = J2Store::platform()->toString($options);
        $symbol = J2Store::currency()->getSymbol();
        $value = str_replace(str_split('\\/:*?"<>|+-'),'',$value);

        // return price input
        $html = '';
        $html .= '<div class="input-group">';
        if (!empty($symbol)) {
            $html .= '<span class="input-group-text">' . $symbol . '</span>';
        }
        $html .= '<input type="text" name="' . $name . '" value="' . $value . '"  ' . $optionvalue . '    />';
        $html .= '</div>';
        J2Store::plugin()->event('PriceInput', [$name, $value, $options, &$html]);
        return $html;
    }

    /**
     * Create a price input field with dynamic data
     */
    public static function price_with_data($prefix, $primary_key, $name, $value, $options, $data)
    {
        $optionvalue = J2Store::platform()->toString($options);
        $symbol = J2Store::currency()->getSymbol();
        $value = str_replace(str_split('\\/:*?"<>|+-'),'',$value);
        // return price input
        $html = '';
        $html .= '<div class="input-group">';
        if (!empty($symbol)) {
            $html .= '<span class="input-group-text">' . $symbol . '</span>';
        }
        $html .= '<input type="text" name="' . $prefix . $name . '" value="' . $value . '"  ' . $optionvalue . '    />';
        $html .= '</div>';
        J2Store::plugin()->event('PriceInputWithData', [$prefix, $primary_key, $name, $value, $options, &$html, $data]);
        return $html;
    }

    /**
     * Creates Checkbox list field
     * @param string $value
     * @param array $data
     * @param array $options
     * @result html with list of checkbox
     */
    /* public static function checkboxList($value,$data,$options=[]){
        $html ='';
        $html .= '<div class="controls">';
        foreach($data as $key =>$value){
            $options['id'] = isset($options['id']) ? $options['id'].'_'.$key : $key;
            $optionvalue = self::attributes($options);
            $html .= '<label class="control-label" for="j2store_input-'.$key.'">';
            $html .='<input type="checkbox" '.$optionvalue.'  value="'.$value.'"     />';
            $html .= $value;
            $html .='</label>';
        }
        $html .='</div>';
    return $html;
    } */

    /**
     * Creates a single checkbox element
     * @param string $name
     * @param unknown_type $value
     * @param array $options
     * @result html
     */
    public static function checkbox($name, $value = null, $options = [])
    {
        return self::input('checkbox', $name, $value, $options);
    }

    /**
     * Create a textarea  field.
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function textarea($name, $value, $options = [])
    {
        return self::input('textarea', $name, $value, $options);
    }

    /**
     * Create a File Field
     * @param string $name
     * @param string $value
     * @param array() $options
     */
    public static function file($name, $value, $options = [])
    {
        return self::input('file', $name, $value, $options);
    }

    /**
     * Creates a email field
     * @param string $name
     * @param unknown_type $value
     * @param array $options
     * @result options
     */
    public static function email($name, $value, $options = [])
    {
        return self::input('email', $name, $value, $options);
    }

    /**
     * Create a select box field.
     *
     * @param string $type The type of the select field
     * @param string $name
     * @param array $list
     * @param string $selected
     * @param array $options
     * @return string
     */
    /* public static function select($type, $name , $value, $id='', $options=[], $relations=[], $placeholder=[]){
        return J2Select::select($type, $name, $value, $id='', $options, $relations, $placeholder);
    } */

    public static function select()
    {
        return new J2Select();
    }

    /**
     * Creates a radio field
     * @param string $name
     * @param string $value
     * @param array $options
     * @result html
     */
    public static function radio($name, $value, $options = [])
    {
        return self::input('radio', $name, $value, $options);
    }

	/**
	 * Creates an inline radio field
	 * @param string $name
	 * @param string $value
	 * @param array $options
	 * @result html
	 */
	public static function inlineRadio($name, $value = '', $options = [])
	{
		$html = '';
		$id = $options['id'] ?? $name;

		// Use HTMLHelper to generate the boolean list with localized labels for yes/no
		$attribs = [];
		$html .= HTMLHelper::_('select.booleanlist', $name, $attribs, $value, Text::_('JYES'), Text::_('JNO'), $id);

		return $html;
	}

    /**
     * Creates a radio boolean  field
     * @param string $name
     * @param string $value
     * @param array $options
     * @result html
     */
	public static function radioBooleanList($name, $value = '', $options = [])
	{
		$html = '';
		$id = $options['id'] ?? $name;

		// Use HTMLHelper to generate the boolean list with localized labels for yes/no
		$attribs = [];
		$html .= HTMLHelper::_('select.booleanlist', $name, $attribs, $value, Text::_('JYES'), Text::_('JNO'), $id);

		return $html;
	}

    /**
     * Create a hidden field
     * @param string $name
     * @param string $value
     * @param array $options
     */
    public static function hidden($name, $value, $options = [])
    {
        return self::input('hidden', $name, $value, $options);
    }

    /**
     * Create a button field
     * @param string $name
     * @param string $value
     * @param array $options
     */
    public static function button($name, $value, $options = [])
    {
        return self::input('button', $name, $value, $options);
    }

	/**
	 * Create a button type field
	 * @param string $name
	 * @param string $value
	 * @param array $options
	 */
	public static function buttontype($name, $value, $options = [])
	{
		return self::input('buttontype', $name, $value, $options);
	}

    /**
     * Creates Media field
     * TODO need to update
     * @param string $name
     * @param string $value
     * @param array $options
     */
    public static function media($name, $value = '', $options = [])
    {
        $platform = J2Store::platform();
        $config = Factory::getApplication()->getConfig();
        $asset_id = $config->get('asset_id');
        //to overcome Permission access Issues to media
        //@front end
        if (J2Store::platform()->isClient('site')) {
            $asset_id = Factory::getApplication()->getConfig('com_content')->get('asset_id');
        }

        $id = isset($options['id']) ? $options['id'] : $name;
        $hide_class = isset($options['no_hide']) ? $options['no_hide'] : 'hide';
        $image_id = isset($options['image_id']) ? $options['image_id'] : 'img' . $id;
        $class = isset($options['class']) ? $options['class'] : '';
        $empty_image = Uri::root() . 'media/j2store/images/common/no_image-100x100.jpg';
        $image = Uri::root();
        $imgvalue = (isset($value) && !empty($value)) ? $value : 'media/j2store/images/common/no_image-100x100.jpg';

        if ($value && file_exists(JPATH_ROOT . '/' . $value)) {
            $folder = explode('/', $value);
            $folder = array_diff_assoc($folder, explode('/', ComponentHelper::getParams('com_media')->get('image_path', 'images')));
            array_pop($folder);
            $folder = implode('/', $folder);
        } else {
            $folder = '';
        }


        if (file_exists(JPATH_SITE . '/' . $imgvalue)) {
            $image .= (isset($value) && !empty($value)) ? $imgvalue : $imgvalue;
        }
        $route = Uri::root();

        $media = ComponentHelper::getParams('com_media');
        $imagesExt  = $media->get('image_extensions') ;
        $audiosExt  = $media->get('audio_extensions');
        $videosExt  = $media->get('video_extensions');
        $documentsExt = $media->get('doc_extensions');
        $displayData = [
            'asset' => 'com_j2store',
            'authorId' => '281',
            'folder' => $folder,
            'link' => '',
            'preview' => 'show',
            'previewHeight' => '200',
            'previewWidth' => '200',
            'class' => $class,
            'id' => 'imageModal_jform_image_' . $id,
            'name' => $name,
            'value' => $value,
            'readonly' => false,
            'disabled' => false,
            'dataAttribute' => '',
            'mediaTypes' => 0,
            'mediaTypeNames' => [],
            'imagesExt' => isset($imagesExt) && !empty($imagesExt) ? explode(',',$imagesExt) : [] ,
            'audiosExt' =>  isset($audiosExt) && !empty($audiosExt) ? explode(',',$audiosExt) : [] ,
            'videosExt' =>  isset($videosExt) && !empty($videosExt) ? explode(',',$videosExt) : [] ,
            'documentsExt' =>  isset($documentsExt) && !empty($documentsExt) ? explode(',',$documentsExt) : [] ,
            'imagesAllowedExt' => [],
            'audiosAllowedExt' => [],
            'videosAllowedExt' => [],
            'documentsAllowedExt' => []
        ];
        $path = JPATH_SITE . '/layouts/joomla/form/field/media.php';
        $media_render = self::getRenderer('joomla.form.field.media', $path);
        $html = $media_render->render($displayData);

        J2Store::plugin()->event('MediaField', [&$html, $name, $value, $options]);
        return $html;
    }

    protected static function getRenderer($layoutId, $path)
    {
        if (empty($layoutId)) {
            $layoutId = 'default';
        }
        $renderer = new FileLayout($layoutId, $basePath = null, $options = ['component' => 'com_j2store']);
        $renderer->setDebug(false);
        $layoutPaths = $renderer->getDefaultIncludePaths();
        if ($layoutPaths) {
            $renderer->setIncludePaths($layoutPaths);
        }
        return $renderer;
    }

    public static function calendar($name, $value, $options = [])
    {
        $id = isset($options['id']) ? $options['id'] : self::clean($name);
        $format = (isset($options['format']) && !empty($options['format'])) ? $options['format'] : '%d-%m-%Y';
        $nullDate = Factory::getContainer()->get('DatabaseDriver')->getNullDate();
        if ($value == $nullDate || empty($value)) {
            $value = $nullDate;
        }
        return HTMLHelper::_('calendar', $value, $name, $id, $format, $options);
    }

    /**
     * @param $href
     * @param $text
     * @param array $options
     * @return string
     */
    public static function link($href, $text, $options = [])
    {
        $href = isset($href) && !empty($href) ? $href : 'javascript:void(0)';
        $icon = isset($options['icon']) && !empty($options['icon']) ? '<i class="' . $options['icon'] . '"></i>' : '';
        $class = isset($options['class']) && !empty($options['class']) ? $options['class'] : '';
        $id = isset($options['id']) && !empty($options['id']) ? $options['id'] : '';
        $onclick = isset($options['onclick']) && !empty($options['onclick']) ? $options['onclick'] : '';
        $html = '<a id="' . $id . '"  href="' . $href . '" class="' . $class . '"';
        if (isset($options['onclick']) && !empty($options['onclick'])) {
            $html .= 'onclick="' . $onclick . '"';
        }

        $html .= '>' . $icon . $text . '</a>';
        return $html;
    }

    /**
     * Create a form input field.
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public static function input($type, $name, $value = null, $options = [])
    {
        //will implode all the options value and return as element attributes
        $optionvalue = J2Store::platform()->toString($options);

        //assign the html
        $html = '';
        //switch the type of input
        switch ($type) {

            // return text input
            case 'text':
                $html .= '<input type="text" name="' . $name . '" value="' . $value . '"  ' . $optionvalue . '    />';
                break;

            //return email input
            case 'email':
                $html .= '<input type="email" name="' . $name . '"  value="' . $value . '"  ' . $optionvalue . '    />';
                break;

            //return password input
            case 'password':
                $html .= '<input type="password"  name="' . $name . '" ' . $optionvalue . '  value="' . $value . '"     />';
                break;

            //return textarea input element
            case 'textarea':
                $html .= '<textarea ' . $optionvalue . ' name="' . $name . '"  value="' . $value . '"     >' . $value . '</textarea>';
                break;

            //return file input element
            case 'file':
                $html .= '<input type="file" name="' . $name . '" ' . $optionvalue . '  value="' . $value . '"     />';
                break;

            //return radio input element
            case 'radio':
                $id = isset($options['id']) && !empty($options['id']) ? $options['id'] : '';
                $html .= J2Html::booleanlist($name, $options, $value, $yes = 'JYES', $no = 'JNO', $id);
                break;

            //return checkbox element
            case 'checkbox':
                $html .= '<input type="checkbox" ' . $optionvalue . '  value="' . $value . '"     />';
                break;

            case 'editor':
                break;

            case 'button':
                $html .= '<input type="button" name="' . $name . '"  ' . $optionvalue . '    value ="' . $value . '"';
                if (isset($options['onclick']) && !empty($options['onclick'])) {
                    $html .= '   onclick ="' . $options['onclick'] . '"';
                }
                $html .= '  />';
                break;

	        case 'buttontype':
		        $html .= '<button type="button" name="' . $name . '"  ' . $optionvalue;
		        if (isset($options['onclick']) && !empty($options['onclick'])) {
			        $html .= '   onclick ="' . $options['onclick'] . '"';
		        }
		        $html .= '>' . $value . '</button>';
		        break;

            case 'submit':
                $html .= '<input type="submit" name="' . $name . '"  ' . $optionvalue . 'value ="' . $value . '" />';
                break;

            case 'hidden':
                $html .= '<input type="hidden" name="' . $name . '" ' . $optionvalue . ' value ="' . $value . '" />';
                break;

            case 'number' :
                $html .= '<input type="number" name="' . $name . '" value="' . $value . '" ' . $optionvalue . ' />';
                break;
        }

        return $html;
    }

    public static function user($name, $value,$options = [])
    {
        $user_field = new UserField();
        $user_field->setValue($value);
        $layout = 'joomla.form.field.user';
        $data = ['name' => $name];
        if(isset($options['required']) && !empty($options['required'])) {
            $data['required'] = $options['required'];
        }
        return $user_field->render($layout, $data);
    }

    public static function generic_list($name, $value, $options)
    {
        $platform = J2Store::platform();
        $platform->loadExtra('behavior.multiselect');
        $id = isset($options['id']) && $options['id'] ? $options['id'] : $name;
        $placeholders = [];
        if(isset($options['options']) && !empty($options['options'])){
            $placeholders = $options['options'];
            unset($options['options']);
        }
        $multiple = false;
        if(isset($options['multiple']) && !empty($options['multiple'])){
            $multiple = true;
        }
        $required = false ;
        if(isset($options['required']) && !empty($options['required'])){
            $required = true;
        }

        $displayData = [
            'class' => '',
            'name' => $name,
            'value' => $value  ,
            'options' =>$placeholders ,
            'autofocus' => '',
            'onchange' => '',
            'dataAttribute' => '',
            'readonly' => '',
            'disabled' => false,
            'hint' => '',
            'required' => $required,
            'id' => '',
            'multiple'=> $multiple
        ];
        $path = JPATH_SITE . '/layouts/joomla/form/field/list-fancy-select.php';
        $media_render = self::getRenderer('joomla.form.field.list-fancy-select', $path);
        return $media_render->render($displayData);
    }

    public static function country($name, $value, $options)
    {
        $country_id = isset($options['id']) && $options['id'] ? $options['id'] : 'country_id';
        $zone_id = isset($options['zone_id']) && $options['zone_id'] ? $options['zone_id'] : 'zone_id';
        $zone_value = isset($options['zone_value']) && $options['zone_value'] ? $options['zone_value'] : '';
        $attr = ["onchange" => "changeZone('$country_id',this.value,'$zone_id',$zone_value)", 'id' => $country_id, 'class' => 'form-select'];
        return self::select()->clearState()
            ->idTag($country_id)
            ->type('genericlist')
            ->name($name)
            ->value($value)
            ->attribs($attr)
            ->hasOne('Countries')
            ->setRelations(
                array(
                    'fields' => array(
                        'key' => 'j2store_country_id',
                        'name' => 'country_name'
                    )
                )
            )->getHtml();
    }

    public static function zone($name, $value, $options)
    {
        $zone_id = isset($options['id']) && $options['id'] ? $options['id'] : 'zone_id';
        $attr = array('id' => $zone_id, 'class' => 'form-select');
        return self::select()->clearState()
            ->idTag($zone_id)
            ->type('genericlist')
            ->name($name)
            ->value($value)
            ->attribs($attr)
            ->setPlaceHolders(array('' => Text::_('J2STORE_SELECT_OPTION')))
            ->hasOne('Zones')
            ->setRelations(
                array(
                    'fields' => array(
                        'key' => 'j2store_zone_id',
                        'name' => 'zone_name'
                    )
                )
            )->getHtml();
    }

    public static function queueKey($name, $value,$options)
    {
        $config = J2Store::config();
        $queue_key = $config->get ( 'queue_key','' );
        $url = 'index.php?option=com_j2store&view=configuration&task=regenerateQueuekey';
        if(empty( $queue_key )){
            $queue_string = Factory::getApplication()->getConfig()->get ( 'sitename','' ).time ();
            $queue_key = md5 ( $queue_string );
            $config->saveOne ( 'queue_key', $queue_key );
        }

        $html = "";
        $html .= "<div class=\"alert alert-block alert-info\"><strong id=\"j2store_queue_key\">".$queue_key."</strong><a onclick=\"regenerateQueueKey()\" class=\"btn btn-primary btn-sm text-white ms-3\"><i class=\"fas fa-solid fa-redo me-2\"></i>".Text::_ ( "J2STORE_STORE_REGENERATE" )."</a>
		<script>
		function regenerateQueueKey(){
            fetch('".$url."', {
                method: 'GET',
                cache: 'no-cache'
            })
            .then(response => response.json())
            .then(json => {
                if (json && json['queue_key']) {
                    document.getElementById('j2store_queue_key').innerHTML = json['queue_key'];
						}
            })
            .catch(error => {
                console.error('Error:', error);
				});
		}
		</script>
		<input type=\"hidden\" name=\"".$name."\" value=\"".$queue_key."\"/>
		</div>";
        return  $html;
    }

    public static function cronLastHit($name,$value,$options)
    {
        $cron_hit = J2Store::config ()->get('cron_last_trigger','');
        if(empty( $cron_hit )){
            $note = Text::_('J2STORE_STORE_CRON_LAST_TRIGGER_NOT_FOUND');
        }elseif(J2Store::utilities ()->isJson ( $cron_hit )){
            $cron_hit = json_decode ( $cron_hit );
            $date =  isset( $cron_hit->date ) ? $cron_hit->date: '';
            $url = isset( $cron_hit->url ) ? $cron_hit->url:'';
            $ip = isset( $cron_hit->ip ) ? $cron_hit->ip:'';
            $note = Text::sprintf('J2STORE_STORE_CRON_LAST_TRIGGER_DETAILS',$date,$url,$ip);
        }
        $html = '';
        $html .= '<strong>'.$note.'</strong>';
        return  $html;
    }

    public static function customLink($name,$value,$options)
    {
        $id = isset($options['id']) && $options['id'] ? $options['id'] : $name;
        $text = isset($options['text']) && $options['text'] ? $options['text'] : '';
        return '<a class="btn btn-primary btn-sm" id="'.$id.'" href="#">'.Text::_($text).'</a>';
    }

    public static function menuItems($name,$value,$options)
    {
        $platform = J2Store::platform();
        $items = $platform->getMenuLinks();

        $groups = [];
        // Build the groups arrays.
        foreach ($items as $menu)
        {
            // Initialize the group.
            $groups[$menu->title] = [];

            // Build the options array.
            foreach ($menu->links as $link)
            {
                $levelPrefix = str_repeat('- ', max(0, $link->level - 1));

                // Displays language code if not set to All
                if ($link->language !== '*')
                {
                    $lang = ' (' . $link->language . ')';
                }
                else
                {
                    $lang = '';
                }
                $groups[$menu->title][] = HTMLHelper::_('select.option',
                    $link->value, $levelPrefix . $link->text . $lang,
                    'value',
                    'text',
                    \in_array($link->type, [])
                );
            }
        }
        $id = isset($options['id']) && $options['id'] ? $options['id'] : $name;
        if(isset($options['id'])){
            unset($options['id']);
        }
        $attr = [
            'id'        => $id,
            'list.select' => $value,
            'option.key.toHtml' => false,
            'option.text.toHtml' => false,
            'group.items' => null,
            'list.attr' => [
                implode(' ',$options),
                'class' => 'form-select'
            ]
        ];
        $html = HTMLHelper::_('select.groupedlist', $groups, $name, $attr);

        return $html;
    }

    public static function inputFieldSql($name,$value,$options)
    {
        $id = isset($options['id']) && $options['id'] ? $options['id'] : $name;
        $unset_values = array(
            'id','key_field','value_field','has_one'
        );
        $has_one = isset($options['has_one']) && $options['has_one'] ? $options['has_one'] : '';
        $key_field = isset($options['key_field']) && $options['key_field'] ? $options['key_field'] : '';
        $value_field = isset($options['value_field']) && $options['value_field'] ? $options['value_field'] : '';
        foreach ($unset_values as $unset_value){
            if(isset($options[$unset_value])){
                unset($options[$unset_value]);
            }
        }
        return self::select()->clearState()
            ->idTag($id)
            ->type('genericlist')
            ->name($name)
            ->value($value)
            ->attribs($options)
            ->hasOne($has_one)
            ->setRelations(
                array(
                    'fields' => array(
                        'key' => $key_field,
                        'name' => $value_field
                    )
                )
            )->getHtml();
    }

    public static function custom($type, $name, $value, $options = [])
    {
        if($type === 'radiolist'){
            $arr = [];
            if(isset($options['options']) && !empty($options['options'])){
                foreach ($options['options'] as $option_key => $option_value){
                    $arr[] = HTMLHelper::_('select.option', $option_key,$option_value);
                }
                unset($options['options']);
            }
            $id = isset($options['id']) && $options['id'] ? $options['id'] : $name;
            $html = J2Html::radiolist($arr, $name, $options, 'value', 'text', $value, $id);

        }elseif ($type === 'list') {
            $html = self::generic_list($name, $value,$options);
        }elseif ($type === 'user') {
            $html = self::user($name, $value,$options);
        }elseif ($type === 'queuekey') {
            $html = self::queueKey($name, $value,$options);
        }elseif ($type === 'cronlasthit') {
            $html = self::cronLastHit($name, $value,$options);
        }elseif ($type === 'customlink') {
            $html = self::customLink($name, $value,$options);
        } elseif ($type === 'country') {
            $html = self::country($name, $value, $options);
        } elseif ($type === 'zone') {
            $html = self::zone($name, $value, $options);
        }elseif ($type === 'fieldsql') {
            $html = self::inputFieldSql($name, $value, $options);
        }elseif ($type === 'menuitem') {
            $html = self::menuItems($name, $value, $options);
        } elseif ($type === 'modal_article') {
            $html = self::article($name, $value, $options);
        } elseif ($type === 'enabled') {
            $id = isset($options['id']) && !empty($options['id']) ? $options['id'] : $name;
            $html = Select::booleanlist($name, $attr = [], $value, $yes = 'JYES', $no = 'JNO', $id);
        }elseif ($type === 'editor') {

	        $id = isset($options['id']) && !empty($options['id']) ? $options['id'] : $name;
	        $width = isset($options['width']) && !empty($options['width']) ? $options['width'] : '100%';
	        $height = isset($options['height']) && !empty($options['height']) ? $options['height'] : '500';
	        $cols = isset($options['cols']) && !empty($options['cols']) ? (int)$options['cols'] : false;
	        $rows = isset($options['rows']) && !empty($options['rows']) ? (int)$options['rows'] : false;
	        $editor_type = isset($options['editor']) && !empty($options['editor']) ? $options['editor'] : '';
	        $editor_content = isset($options['content']) && !empty($options['content']) ? $options['content'] : '';

	        if ($editor_content === 'from_file' && !empty($value)) {
		        $content = self::getSource($value);
		        $value = $content->source;
	        }

            if ($editor_type) {
                $editor = Editor::getInstance($editor_type);
			} else {
                $config = Factory::getApplication()->getConfig();
	            $defaultEditor = $config->get('editor');
	            $editor = Editor::getInstance($defaultEditor);
			}

	        $buttons = isset($options['buttons']) ? $options['buttons'] : false; // Default to true (enable all buttons)
	        $html = $editor->display($name, $value, $width, $height, $cols, $rows, false, $id, null, $buttons, $options);
        } elseif ($type === 'filelist'){
            $file_options = array(
                'options' => array(
                    '' => Text::_('J2STORE_CHOOSE')
                )
            );
            $fileFilter = isset($options['filter']) && !empty($options['filter']) ? $options['filter']: '';
            $path = isset($options['directory']) && !empty($options['directory']) ? $options['directory']: '';
            if (!is_dir($path))
            {
                $path = JPATH_ROOT . '/' . $path;
            }

            $path = Path::clean($path);
            $files = Folder::files($path, $fileFilter);
            if (is_array($files))
            {
                foreach ($files as $file)
                {
                    $file_options['options'][$file] = $file;
                }
            }

            $html = self::generic_list($name, $value,$file_options);
        } elseif ($type === 'calendar') {
            $html = self::calendar($name, $value, $options);
        } elseif ($type === 'coupondiscounttypes') {
            $html = self::couponDiscountTypes($name, $value, $options);
        }elseif ($type === 'couponproducts') {
            $html = self::couponProduct($name, $value, $options);
        }elseif ($type === 'duallistbox') {
            $html = self::duallistbox($name, $value, $options);
        }elseif ($type === 'usergroup') {
            $html = self::userGroup($name, $value,$options);
        } else {
            $html = self::input('text', $name, $value, $options);
        }
        return $html;
    }

    public static function userGroup($name, $value,$options)
    {
        $platform = J2Store::platform();
        $platform->loadExtra('behavior.multiselect');

        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->getQuery(true);
        $query->select('a.id AS value, a.title AS text');
        $query->from('#__usergroups AS a');
        $query->group('a.id, a.title');
        $query->order('a.id ASC');
        $query->order($query->qn('title') . ' ASC');

        // Get the options.
        $db->setQuery($query);
        $user_group = $db->loadObjectList();

        $id = isset($options['id']) && $options['id'] ? $options['id'] : $name;
        $placeholders = [];
        if(isset($user_group) && !empty($user_group)){
            $placeholders = $user_group;
        }

        $displayData = array(
            'class' => '',
            'name' => $name,
            'value' => $value  ,
            'options' =>$placeholders ,
            'autofocus' => '',
            'onchange' => '',
            'dataAttribute' => '',
            'readonly' => '',
            'disabled' => false,
            'hint' => '',
            'required' => false,
            'id' => '',
            'multiple'=> true
        );
        $path = JPATH_SITE . '/layouts/joomla/form/field/list-fancy-select.php';
        $media_render = self::getRenderer('joomla.form.field.list-fancy-select', $path);

        return $media_render->render($displayData);
    }

    protected static function getSource($filename)
    {
        $app = Factory::getApplication();
        $item = new \stdClass();

        if ($filename) {
            $filePath = Path::clean(JPATH_ADMINISTRATOR.'/components/com_j2store/views/emailtemplate/tpls/'.$filename);
            if (file_exists($filePath)) {
                $item->filename = $filename;
                $item->source = file_get_contents($filePath);
            } else {
                $app->enqueueMessage(Text::_('J2STORE_EMAILTEMPLATE_ERROR_SOURCE_FILE_NOT_FOUND'), 'error');
            }
        }
        return $item;
    }
    public static function duallistbox($name, $value, $options)
    {
        $platform = J2Store::platform();
        FormHelper::loadFieldClass('list');
        $json = self::getOptions($name, $value, $options);
        $json = json_encode($json,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        HTMLHelper::_('script', 'media/j2store/js/dual-list-box.js', false, false);
        HTMLHelper::_('stylesheet', 'media/j2store/css/dual-list-box.css', false, false);
        $selected = json_encode($value);
        $input_id = !empty($options->id) ? $options->id : 'duallistbox-input';
        $html ='<div id="dual-list-box" class="row-fluid">';
        $html .='<select id='.$input_id.' multiple="multiple"
					size ="10"  name='.$name.'[]'.'
					>
                    </select>';
        $html .='<script type="text/javascript">
	   var optionList = document.getElementById(\''.$input_id.'\').options;
              var options = '.$json.'
              var selected = '. $selected .'
              options.forEach(option => {
               if (option.title && option.id ) {
                  optionList.add(new Option(option.title, option.id, option.selected));
               }
             });
		(function($){
        var values = '. $selected .';
        if ( values != null ){
        $.each(values.split(","), function(i,e){
        $("#' . $input_id . ' option[value=\'" + e + "\']").attr("selected", true);
        });
        }
		var dualListObj =  $(\'#'.$input_id.'\').bootstrapDualListbox();
		})(j2store.jQuery)
		</script></div>';
        return $html;
    }

    public static function duallistboxnew($name, $value, $options)
    {
        $platform = J2Store::platform();
        FormHelper::loadFieldClass('list');
        $json = self::getOptions($name, $value, $options);
        $json = json_encode($json,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        HTMLHelper::_('script', 'media/j2store/js/dual-listbox.js', false, false);
        HTMLHelper::_('stylesheet', 'media/j2store/css/dual-listbox.css', false, false);
        $selected = json_encode($value);
        $input_id = !empty($options->id) ? $options->id : 'duallistbox-input';
        $html ='<div id="dual-list-box" class="row-fluid">';
        $html .='<select id="'.$input_id.'" multiple="multiple" size ="10"  name='.$name.'[]'.'></select>';
        $html .= '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
            var options = ' . $json . ';
            var selectElement = document.getElementById("' . $input_id . '");

            // Add options to the select element
            options.forEach(option => {
                if (option.title && option.id) {
                    var newOption = new Option(option.title, option.id, option.selected);
                    selectElement.add(newOption);
                }
            });

            // Initialize DualListbox
            new DualListbox(selectElement, {
                availableTitle: "Available Options",
                selectedTitle: "Selected Options",
                addButtonText: ">",
                removeButtonText: "<",
                addAllButtonText: ">>",
                removeAllButtonText: "<<",
                sortable: true,
                upButtonText: "ᐱ",
                downButtonText: "ᐯ",
                draggable: true
            });

        });
        </script></div>';
        return $html;
    }

    public static function getOptions($name, $value, $options)
    {
        $source_file      = empty($options['source_file']) ? '' : (string) $options['source_file'];
        $source_class     = empty($options['source_class']) ? '' : (string) $options['source_class'];
        $source_method    = empty($options['source_method']) ? '' : (string) $options['source_method'];
        $source_key       = empty($options['source_key']) ? '*' : (string) $options['source_key'];
        $source_value     = empty($options['source_value']) ? '*' : (string) $options['source_value'];
        $source_translate = empty($options['source_translate']) ? 'true' : (string) $options['source_translate'];
        $source_translate = in_array(strtolower($source_translate), array('true','yes','1','on')) ? true : false;
        $source_format	  = empty($options['source_format']) ? '' : (string) $options['source_format'];

        //echo $source_method;
        $option = [];
        {
            // Maybe we have to load a file?
            if (!empty($source_file))
            {
                $source_file = F0FTemplateUtils::parsePath($source_file, true);

                if (F0FPlatform::getInstance()->getIntegrationObject('filesystem')->fileExists($source_file))
                {
                    include_once $source_file;
                }
            }
            // Make sure the class exists
            if (class_exists($source_class, true))
            {				// ...and so does the option
                if (in_array($source_method, get_class_methods($source_class)))
                {
                    // Get the data from the class
                    if ($source_format === 'optionsobject')
                    {
                        $option = array_merge($option, $source_class::$source_method());
                    }
                    else
                    {
                        // Get the data from the class
                        $source_data = $source_class::$source_method();

                    }
                }
            }
        }
        //$group_options[] = [];
        //to avoid jquery error
        foreach($source_data as $cat){
            //$group_options[$cat->title] =($cat->title);
            $source_data[] =Text ::_ ( strtoupper( $cat->title));
        }

        return $source_data ;
    }

    public static function couponProduct($name, $value, $options)
    {
        $html ='';
        $fieldId = isset($options['id']) ? $options['id'] : 'jform_product_list';
        $html =J2StorePopup::popup("index.php?option=com_j2store&view=coupons&task=setProducts&layout=products&tmpl=component&function=jSelectProduct&field=".$fieldId, Text::_( "J2STORE_SET_PRODUCTS" ), array('width'=>800 ,'height'=>400 ,'class'=>'btn btn-success'));
        return $html ;
    }

    public  static function couponDiscountTypes($name, $value, $options)
    {
        $model = J2Store::fof()->getModel( 'Coupons', 'J2StoreModel' );
        $list = $model->getCouponDiscountTypes ();
        $attr = array ();
        // Get the field options.
        // Initialize some field attributes.
        $attr['class'] = !empty($options->class) ? $options->class : 'form-select';
        // Initialize JavaScript field attributes.
        $attr ['onchange'] = isset ( $options->onchange ) ? $options->onchange : '';
        $attr ['id'] = isset ( $options->id ) ? $options->id : '';

        // generate country filter list
        return J2Html::select ()->clearState ()->type ( 'genericlist' )->name ( $name )->attribs ( $attr )->value ( $value )->setPlaceHolders ( $list )->getHtml ();
    }

    public static function getEditor($editor='')
    {
        if(empty($editor)){
            $editor = Factory::getApplication()->get('editor');
            if(empty($editor)) $editor = null;
        }
        $my_editor = Editor::getInstance($editor);

        $my_editor->initialise();
        return $my_editor;
    }

	// Kept to avoid b/c breaks;
	public static function artical($name, $value, $options)
	{
		return self::article($name, $value, $options);
	}

  public static function article($name, $value, $options)
	{
        $platform = J2Store::platform();
        //
        $allowClear     = true;
        $allowSelect    = true;
        $languages = LanguageHelper::getContentLanguages(array(0, 1), false);
        $app = $platform->application();
        // Load language
        Factory::getApplication()->getLanguage()->load('com_content', JPATH_ADMINISTRATOR);

        // The active article id field.
        $value = (int) $value ?: '';
        $id = isset($options['id']) && !empty($options['id']) ? $options['id']: $name;
        $required = (int)isset($options['required']) && !empty($options['required']) ? $options['required']: false;
        $modalId = 'Article_' . $id;

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        // Add the modal field script to the document head.
        $wa->useScript('field.modal-fields');

        // Script to proxy the select modal function to the modal-fields.js file.
        if ($allowSelect)
        {
            static $scriptSelect = null;

            if (is_null($scriptSelect))
            {
                $scriptSelect = [];
            }

            if (!isset($scriptSelect[$id])) {
                $wa->addInlineScript("
				window.jSelectJ2Article_" . $id . " = function (id, title, catid, object, url, language) {
					window.processModalSelect('Article', '" . $id . "', id, title, catid, object, url, language);
					jQuery('body').removeClass('modal-open');
                    jQuery('.modal-backdrop').remove();
				}",
                    [],
                    ['type' => 'module']
                );
                Text::script('JGLOBAL_ASSOCIATIONS_PROPAGATE_FAILED');

                $scriptSelect[$id] = true;
            }
        }

        // Setup variables for display.
        $linkArticles = 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';
        $urlSelect = $linkArticles . '&amp;function=jSelectJ2Article_' . $id;
        if ($value)
        {
            $db = Factory::getContainer()->get('DatabaseDriver');

            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__content'))
                ->where($db->quoteName('id') . ' = :value')
                ->bind(':value', $value);
            $db->setQuery($query);

            try
            {
                $title = $db->loadResult();
            }
            catch (\RuntimeException $e)
            {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        $title = empty($title) ? Text::_('COM_CONTENT_SELECT_AN_ARTICLE') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        $html = '<span class="input-group">';
        $html .= '<input class="form-control" id="' . $id . '_name" type="text" value="' . $title . '" readonly size="35">';
        if ($allowSelect)
        {
            $html .= '<button'
                . ' class="btn btn-primary' . ($value ? ' hidden' : '') . '"'
                . ' id="' . $id . '_select"'
                . ' data-bs-toggle="modal"'
                . ' type="button"'
                . ' data-bs-target="#ModalSelect' . $modalId . '">'
                . '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
                . '</button>';
        }
        // Clear article button
        if ($allowClear)
        {
            $html .= '<button'
                . ' class="btn btn-secondary' . ($value ? '' : ' hidden') . '"'
                . ' id="' . $id . '_clear"'
                . ' type="button"'
                . ' onclick="window.processModalParent(\'' . $id . '\'); return false;">'
                . '<span class="icon-times" aria-hidden="true"></span> ' . Text::_('JCLEAR')
                . '</button>';
        }

        $html .= '</span>';
        $modalTitle    = Text::_('COM_CONTENT_SELECT_AN_ARTICLE');
        // Select article modal
        $html .= HTMLHelper::_(
            'bootstrap.renderModal',
            'ModalSelect' . $modalId,
            array(
                'title'       => $modalTitle,
                'url'         => $urlSelect,
                'height'      => '400px',
                'width'       => '800px',
                'bodyHeight'  => 70,
                'modalWidth'  => 80,
                'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
                    . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
            )
        );

        $class = $required ? ' class="required modal-value"' : '';
        $html .= '<input type="hidden" id="' . $id . '_id" ' . $class . ' data-required="' . (int) $required . '" name="' . $name
            . '" data-text="' . htmlspecialchars(Text::_('COM_CONTENT_SELECT_AN_ARTICLE'), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '">';

        return $html;
    }

    public static function getOrderStatusHtml($id)
    {
        $html = '';
        $item = J2Store::fof()->getModel('OrderStatuses', 'J2StoreModel')->getItem($id);
        if ($id) {
            $html .= '<label class="label badge ' . $item->orderstatus_cssclass . '">' . Text::_($item->orderstatus_name) . '</label>';
        }
        return $html;
    }

    public static function getUserNameById($id)
    {
        $userFactory = Factory::getContainer()->get(UserFactory::class);
        $user = $userFactory->loadUserById($id);
        return $user->name;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     * @return string
     */
    public static function attributes($attributes)
    {
        $html = [];

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array)$attributes as $key => $value) {

            $element = self::attributeElement($key, $value);

            if (!is_null($element)) $html[] = $element;
        }
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    protected static function attributeElement($key, $value)
    {
        if (is_numeric($key)) $key = $value;

        if (!is_null($value))

            return $key . '="' . ($value) . '"';
    }

    public static function booleanlist($name, $attribs = [], $selected = null, $yes = 'JYES', $no = 'JNO', $id = false)
    {
        $arr = array(HTMLHelper::_('select.option', '0', Text::_($no)), HTMLHelper::_('select.option', '1', Text::_($yes)));

        return J2Html::radiolist($arr, $name, $attribs, 'value', 'text', (int)$selected, $id);
    }

    public static function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    /**
     * Generates an HTML radio list.
     *
     * @param array $data An array of objects
     * @param string $name The value of the HTML name attribute
     * @param string $attribs Additional HTML attributes for the <select> tag
     * @param mixed $optKey The key that is selected
     * @param string $optText The name of the object variable for the option value
     * @param string $selected The name of the object variable for the option text
     * @param boolean $idtag Value of the field id or null by default
     * @param boolean $translate True if options will be translated
     *
     * @return  string  HTML for the select list
     *
     * @since   1.5
     */
    public static function radiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
    {
        reset($data);

        $id = isset($attribs['id']) && !empty($attribs['id']) ? $attribs['id'] : '';
        $class = isset($attribs['class']) && !empty($attribs['class']) ? $attribs['class'] : '';


        if (is_array($attribs)) {
            $attribs = J2Store::platform()->toString($attribs);
        }

        $id_text = $idtag ? $idtag : self::clean($name);

            $html = '<div class="'.$class.' radio">';
            foreach ($data as $obj) {
                $k = $obj->$optKey;
                $t = $translate ? Text::_($obj->$optText) : $obj->$optText;
                $id = (isset($obj->id) ? $obj->id : null);

                $extra = '';
                $id .= $id ? $obj->id : $id_text . $k;

                if (is_array($selected)) {
                    foreach ($selected as $val) {
                        $k2 = is_object($val) ? $val->$optKey : $val;

                        if ($k == $k2) {
                            $extra .= ' selected="selected" ';
                            break;
                        }
                    }
                } else {
                    $extra .= ((string)$k == (string)$selected ? ' checked="checked" ' : '');

                }
                $input_class = ($class === 'btn-group' ? ' class="btn-check" ' : '');
                $label_class = ($class === 'btn-group' ? 'btn btn-outline-success' : 'radio');

                $html .= "\n\t\n\t" . '<input type="radio"  '.$input_class.'  name="' . $name . '" id="' . $id . '"  value="' . $k . '" ' . $extra
                    . $attribs . ' />' ;
                $html .= "\n\t" . '<label class="'. $label_class.'"  for="' . $id . '" id="' . $id . '-lbl" >'. $t;
                $html .= "\n\t" . '</label>';
            }

            $html .= "\n";
            $html .= '</div>';
            $html .= "\n";

        return $html;
    }

    public static function checkboxlist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
    {
        reset($data);

        if (is_array($attribs)) {
            $attribs = J2Store::platform()->toString($attribs);
        }

        $id_text = $idtag ? $idtag : $name;

        $html = '<div class="checkbox">';

        foreach ($data as $obj) {
            $k = $obj->$optKey;
            $t = $translate ? Text::_($obj->$optText) : $obj->$optText;
            $id = (isset($obj->id) ? $obj->id : null);

            $extra = '';
            $id = $id ? $obj->id : $id_text . $k;

            if (is_array($selected)) {
                foreach ($selected as $val) {
                    $k2 = is_object($val) ? $val->$optKey : $val;

                    if ($k == $k2) {
                        $extra .= ' selected="selected" ';
                        break;
                    }
                }
            } else {
                $extra .= ((string)$k == (string)$selected ? ' checked="checked" ' : '');
            }

            $html .= "\n\t" . '<label for="' . $id . '" id="' . $id . '-lbl" class="checkbox">';
            $html .= "\n\t\n\t" . '<input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $k . '" ' . $extra
                . $attribs . ' >' . $t;
            $html .= "\n\t" . '</label>';
        }

        $html .= "\n";
        $html .= '</div>';
        $html .= "\n";

        return $html;
    }

	public static function checkboxswitch($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		reset($data);

		if (is_array($attribs)) {
			$attribs = J2Store::platform()->toString($attribs);
		}

		$id_text = $idtag ? $idtag : $name;

		$html = '<div class="form-check form-switch">';

		foreach ($data as $obj) {
			$k = $obj->$optKey;
			$t = $translate ? Text::_($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);

			$extra = '';
			$id = $id ? $obj->id : $id_text . $k;

			if (is_array($selected)) {
				foreach ($selected as $val) {
					$k2 = is_object($val) ? $val->$optKey : $val;

					if ($k == $k2) {
						$extra .= ' selected="selected" ';
						break;
					}
				}
			} else {
				$extra .= ((string)$k == (string)$selected ? ' checked="checked" ' : '');
			}

			$html .= '<input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $k . '" ' . $extra. $attribs . ' >';
			$html .= '<label for="' . $id . '" id="' . $id . '-lbl" class="form-check-label">';
			$html .= $t;
			$html .= '</label>';
		}
		$html .= '</div>';

		return $html;
	}

    /**
     * Method to return PRO feature notice
     *
     * @return string
     */
    public static function pro()
    {
        if (!class_exists('J2Store')) {
            require_once JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/j2store.php';
        }
        $view = J2Store::view();
        $view->setDefaultViewPath(JPATH_ADMINISTRATOR . '/components/com_j2store/views/eupdates/tmpl');
        $html = $view->getOutput('profeature');
        return $html;
    }

    public static function list_custom($type, $name, $field, $item)
    {
        $html = '';
        if ($type === 'couponexpiretext') {
            $html = self::couponExpireText($item);
        } elseif ($type === 'fieldsql') {
            $html = self::fieldSQL($name, $field, $item);
        } elseif ($type === 'corefieldtypes') {
            $html = self::fieldCore($name, $field, $item);
        }elseif ($type === 'receivertypes') {
            $html = self::receiverTypes($item);
        } elseif ($type === 'orderstatuslist'){
            $html = self::orderStatusList($item);
        } elseif ($type === 'shipping_link'){
            $html = self::shippingLink($item,$field);
        }
        return $html;
    }

    public static function couponExpireText($item)
    {
        $info_class = 'badge bg-info';
        $warning_class = 'badge bg-warning';
        $success_class = 'badge bg-success';

        if (!isset($item->valid_from) || !isset($item->valid_to)) {
            return '';
        }
        $diff = self::getExpiryDate($item->valid_from, $item->valid_to);
        $style = 'style="padding:5px"';
        if ($diff->format("%R%a") == 0) {
            $text = Text::sprintf('COM_J2STORE_COUPON_WILL_EXPIRE_TODAY', $diff->format("%a") . ' day (s) ');
            $html = '<label class="'.$info_class .'" ' . $style . '>' . $text . '</label>';
        } elseif ($diff->format("%R%a") <= 0) {
            $text = Text::sprintf('COM_J2STORE_COUPON_EXPIRED_BEFORE_DAYS', $diff->format("%a") . ' day (s) ');
            $html = '<label class="'.$warning_class .'" ' . $style . '>' . $text . '</label>';
        } else {
            $text = Text::sprintf('COM_J2STORE_COUPON_WILL_EXPIRE_WITH_DAYS', $diff->format("%a") . ' day (s) ');
            $html = '<label class="'.$success_class.'" ' . $style . '>' . $text . '</label>';
        }
        return $html;
    }

    protected static function getExpiryDate($valid_from, $valid_to)
    {
        $start = date("Y-m-d");
        $today = date_create($start);
        //assign the coupon offer start date
        // Assign the coupon valid date
        $date2 = date_create($valid_to);
        return date_diff($today, $date2);
    }

	public static function fieldSQL($name, $field, $item)
	{
		$html = '';
		$query = isset($field['query']) && !empty($field['query']) ? $field['query'] : '';

		// Verify that the query includes a SELECT clause
		if (strpos(strtoupper($query), 'SELECT') === false) {
			$query = 'SELECT * FROM ' . $query;
		}

		if (!empty($field['key_field']) && !empty($query) && !empty($item->$name)) {
			// Get the database instance
			$db = Factory::getContainer()->get('DatabaseDriver');

			// Properly escape column and value
			$query .= ' WHERE ' . $db->quoteName($field['key_field']) . ' = ' . $db->quote($item->$name);
		}

		if (!empty($query)) {
			try {
				$db = Factory::getContainer()->get('DatabaseDriver');
				$field_data = $db->setQuery($query)->loadObject();
				$value_field = $field['value_field'] ?? '';
				$html = $field_data->$value_field ?? '';
			} catch (Exception $e) {
				// Log or handle the error as needed
				Factory::getApplication()->enqueueMessage('Error executing query: ' . $e->getMessage(), 'error');
			}
		}

		return $html;
	}

    public static function receiverTypes($item)
    {
        $html ='';

        $list = array(
            '*' => Text::_( 'J2STORE_EMAILTEMPLATE_RECEIVER_OPTION_BOTH' ),
            'admin'=> Text::_( 'J2STORE_EMAILTEMPLATE_RECEIVER_OPTION_ADMIN' ),
            'customer'=>Text::_( 'J2STORE_EMAILTEMPLATE_RECEIVER_OPTION_CUSTOMER')
        );

        if(empty($item->receiver_type)) $item->receiver_type = '*';
        $html .= $list[$item->receiver_type];
        return $html;
    }

    public static function orderStatusList($item)
    {
        $success_class = 'badge bg-success';
        $html ='';
        if($item->orderstatus_id !== '*'){
            $orderstatus = J2Store::fof()->loadTable('Orderstatus', 'J2StoreTable');
            $orderstatus->load($item->orderstatus_id);
            $html = '<label class="label">' . Text::_($orderstatus->orderstatus_name);
            if (isset($orderstatus->orderstatus_cssclass) && $orderstatus->orderstatus_cssclass) {
            if($orderstatus->orderstatus_cssclass === 'label-success'){
                    $label_class = 'badge bg-success';
            }else if($orderstatus->orderstatus_cssclass === 'label-warning'){
                    $label_class = 'badge bg-warning';
            }else if($orderstatus->orderstatus_cssclass === 'label-important'){
                    $label_class = 'badge bg-important';
            }else if($orderstatus->orderstatus_cssclass === 'label-info'){
                    $label_class = 'badge bg-info';
                }
                $html = '<label class="' .$label_class. '">' . Text::_($orderstatus->orderstatus_name);
            }
        }else{
            $html ='<label class="'.$success_class.'">'.Text::_('J2STORE_ALL');
        }
        $html .='</label>';
        return $html;
    }

    public static function shippingLink($item,$field)
    {
        $url = '';
        if(empty($item) || !isset($field['label'])){
            return $url;
        }
        $custom_element = array('shipping_standard');
        J2Store::plugin()->event('IsJ2StoreCustomShippingPlugin',array(&$custom_element));
        if(isset($item->element) && isset($item->link_edit) && in_array($item->element,$custom_element)){
            $url = $item->link_edit;
        }elseif (isset($item->element) && isset($item->plugin_link_edit)){
            $url = $item->plugin_link_edit;
        }
        $text = isset($field['translate']) && $field['translate'] ? Text::_($field['label']):$field['label'];
        return '<a href="'.$url.'">'.$text.'</a>';
    }

    public static function fieldCore($name, $field, $item)
    {
        $html ='<label class="badge bg-warning">'.Text::_('J2STORE_CUSTOM_FIELDS_NOT_CORE').'</label>';
        if(isset($item->$name) && $item->$name){
            $html = '<label class="badge bg-success">'.Text::_('J2STORE_CUSTOM_FIELDS_CORE').'</label>';
        }
        return $html;
    }

	public static function calculateDaysFromStartDate($customer_start_date)
	{
		$startDate = new DateTime($customer_start_date);
		$currentDate = new DateTime();
		$interval = $startDate->diff($currentDate);
		return $interval->days;
	}

	public static function getCustomerStartDate($orders)
	{
		if (!empty($orders) && isset($orders[0]->created_on)) {
			return $orders[0]->created_on;
		}
		return null;
	}

	public static function getGrossCustomerSales($orders)
	{
		$total = 0;
		foreach ($orders as $order) {
			// Check if order status_cssclass is set and does not contain 'danger', 'important', or 'warning'
			if (isset($order->orderstatus_cssclass) &&
                strpos($order->orderstatus_cssclass, 'danger') === false &&
                strpos($order->orderstatus_cssclass, 'important') === false &&
                strpos($order->orderstatus_cssclass, 'warning') === false)
			{
				$total += (float) $order->order_total;
			}
		}
		return $total;
	}

	public static function getSumCustomerOrders($orders)
	{
		foreach ($orders as $order) {
			// Ensure order status_cssclass is set and does not contain 'danger', 'important', or 'warning'
			if (isset($order->orderstatus_cssclass) &&
                strpos($order->orderstatus_cssclass, 'danger') === false &&
                strpos($order->orderstatus_cssclass, 'important') === false &&
                strpos($order->orderstatus_cssclass, 'warning') === false)
			{
				$count++;
			}
		}
		return $count;
	}

	public static function countUserOrders($userId)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__j2store_orders'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId);
		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public static function getUserOrders($user_id)
	{
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__j2store_orders'))
			->where($db->quoteName('user_id') . ' = ' . (int) $user_id);
		$db->setQuery($query);
		$orders = $db->loadObjectList();
		return $orders;
	}
}

class J2Select extends Registry
{
    protected $state;
    protected $options;

    public function __construct($properties = null)
    {
        if (!is_object($this->state)) {
            $this->state = new JObject();
        }
        $this->options = [];
        parent::__construct($properties);
    }

    /**
     * Magic getter; allows to use the name of model state keys as properties
     *
     * @param string $name The name of the variable to get
     *
     * @return  mixed  The value of the variable
     */
    public function __get($name)
    {
        return $this->getState($name);
    }

    /**
     * Magic setter; allows to use the name of model state keys as properties
     *
     * @param string $name The name of the variable
     * @param mixed $value The value to set the variable to
     *
     * @return  void
     */
    public function __set($name, $value)
    {
        return $this->setState($name, $value);
    }

    /*
    * Magic caller; allows to use the name of model state keys as methods to
    * set their values.
    *
    * @param   string  $name       The name of the state variable to set
    * @param   mixed   $arguments  The value to set the state variable to
    *
    * @return  J2Select  Reference to self
    */
    public function __call($name, $arguments)
    {
        $arg1 = array_shift($arguments);
        $this->setState($name, $arg1);

        return $this;
    }

    /**
     * Method to set model state variables
     *
     * @param string $property The name of the property.
     * @param mixed $value The value of the property to set or null.
     *
     * @return  mixed  The previous value of the property or null if not set.
     */
    public function setState($property, $value = null)
    {
        return $this->state->set($property, $value);
    }

    /**
     * Method to set model state variables
     *
     * @param string $property The name of the property.
     * @param mixed $value The value of the property to set or null.
     *
     * @return  mixed  The previous value of the property or null if not set.
     */
    public function getState($property = null, $default = null)
    {
        return $property === null ? $this->state : $this->state->get($property, $default);
    }

    public function clearState()
    {
        $this->state = new JObject();
        return $this;
    }

    public function getHtml()
    {

        $html = '';

        $state = $this->getState();

        $value = isset($state->value) ? $state->value : '';
        $attribs = isset($state->attribs) ? $state->attribs : [];

        $placeholder = isset($state->placeholder) ? $state->placeholder : [];

        if (isset($state->hasOne)) {
            $modelName = $state->hasOne;
            $model = J2Store::fof()->getModel($modelName, 'J2StoreModel');

            //check relations
            if (isset($state->primaryKey) && isset($state->displayName)) {
                $primary_key = $state->primaryKey;
                $displayName = $state->displayName;

            } else {
                $primary_key = $model->getTable()->getKeyName();
                $knownFields = $model->getTable()->getKnownFields();
                $displayName = $knownFields[1];
            }

            if (isset($state->ordering) && !empty($state->ordering)) {
                $model->setState('filter_order', $state->ordering);
            }

            $items = $model->enabled(1)->getList();

            if (count($items)) {
                foreach ($items as $item) {
                    if (is_array($displayName)) {
                        $text = '';
                        foreach ($displayName as $n) {
                            if (isset($item->$n)) $text .= Text::_($item->$n) . ' ';
                        }
                    } else {
                        $text = Text::_($item->$displayName);
                    }
                    $this->options[] = HTMLHelper::_('select.option', $item->$primary_key, $text);
                }
            }
        }

        $idTag = isset($state->idTag) ? $state->idTag : 'j2store_' . F0FInflector::underscore($state->name);

        return HTMLHelper::_('select.' . $state->type, $this->options, $state->name, $attribs, 'value', 'text', $value, $idTag);
    }

    public function setRelations($relations = [])
    {

        $state = $this->getState();

        if (is_array($relations) && isset($relations['fields']) && count($relations['fields'])) {
            $primary_key = $relations['fields']['key'];
            $displayName = $relations['fields']['name'];
        }
        $this->setState('primaryKey', $primary_key);
        $this->setState('displayName', $displayName);
        return $this;
    }

    public function setPlaceholders($placeholders = [])
    {
        //placeholder
        if (is_array($placeholders) && count($placeholders)) {
            foreach ($placeholders as $k => $v) {
                $this->options[] = HTMLHelper::_('select.option', $k, $v);
            }
        } else {
            $this->options[] = HTMLHelper::_('select.option', '', Text::_('J2STORE_SELECT_OPTION'));
        }

        return $this;
    }
}
