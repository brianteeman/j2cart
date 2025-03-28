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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;

/**
 * J2Store helper.
 */
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2store.php');

class J2StoreHelperSelect
{
	/**
	 * Method to get array
	 * @param unknown_type $view
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @param unknown_type $value1
	 * @return multitype:string NULL
	 */
	function getSelectArrayOptions($view,$key,$value,$value1='')
    {
		$items = J2Store::fof()->getModel(ucfirst($view),'J2storeModel')->enabled(1)->getList();
		$result = [];
		$result[''] = Text::_('J2STORE_SELECT_OPTION');
		foreach($items as $item){
			$result[$item->$key] = $item->$value;
			if(isset($value1) && !empty($value1)){
				$result[$item->$key] = $item->$value . $item->$value1;
			}
		}
		return $result;
	}

	public static function productattributeoptionprefix( $selected, $name = 'filter_prefix', $attribs = array('class' => 'j2storeprefix form-select', 'size' => '1'), $idtag = null, $allowAny = false, $title = 'Select Prefix' )
	{
		$list = [];
		if($allowAny) {
			$list[] =  self::option('', "- ".Text::_( $title )." -" );
		}

		$list[] = HTMLHelper::_('select.option',  '+', "+" );
		$list[] = HTMLHelper::_('select.option',  '-', "-" );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}

	protected static function genericlist($list, $name, $attribs, $selected, $idTag)
    {
		if (empty($attribs)) {
			$attribs = [];
		}

		if (!isset($attribs['class'])) {
			$attribs['class'] = 'form-select';
		} else {
			$attribs['class'] = $attribs['class'] . ' form-select';
		}

		return HTMLHelper::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	// get countries
	public static function getCountries()
    {
		$options = array ();
		$enabled = 1;
		$countries = J2Store::fof()->getModel('countries', 'J2StoreModel')->enabled($enabled)->getList();
		foreach ( $countries as $country ) {
			$options [$country->j2store_country_id] = Text::_($country->country_name);
		}
		return $options;
	}

	// get taxrates
	public static function getTaxRates()
    {
		$options = array ();
		$enabled = 1;
		$taxrates = J2Store::fof()->getModel('taxrates', 'J2StoreModel')->enabled($enabled)->getList();

		foreach ( $taxrates as $taxrate ) {
			$options [$taxrate->j2store_taxrate_id] = $taxrate->taxrate_name;
		}
		return $options;
	}

	// get languages
	public static function languages($selected = null, $id = 'language', $attribs = array())
    {
		$languages = LanguageHelper::getLanguages('lang_code');
		$options = array ();

		if (isset ( $attribs ['allow_empty'] )) {
			if ($attribs ['allow_empty']) {
				$options [] = HTMLHelper::_( 'select.option', '', '- ' . Text::_( 'JALL_LANGUAGE' ) . ' -' );
			}
		}

		$options [] = HTMLHelper::_( 'select.option', '*', Text::_( 'JALL_LANGUAGE' ) );
		if (! empty ( $languages ))
			foreach ( $languages as $key => $lang ) {
				$options [] = HTMLHelper::_( 'select.option', $key, $lang->title );
			}

		return self::genericlist ( $options, $id, $attribs, $selected, $id );
	}

	// get orderstatus
	public static function OrderStatus($selected = null, $id = '', $attribs = array(), $default_option = null)
    {
		$orderstatus_options [] = HTMLHelper::_( 'select.option', '', Text::_( 'JALL' ) );

		$orderlist = self::getOrderStatus ( $default_option, true );
		foreach ( $orderlist as $row ) {
			$orderstatus_options [] = HTMLHelper::_( 'select.option', $row->j2store_orderstatus_id, $row->order_name );
		}
		return self::genericlist ( $orderstatus_options, $id, $attribs, $selected, $id );
	}

	/**
	 * Static method that return only the orderstatus.
	 */
	public static function getOrderStatus($default_option = null, $asObject = false)
    {
		$enabled = 1;
		$orderstatus = J2Store::fof()->getModel('orderstatuses', 'J2StoreModel')->enabled($enabled)->getList(true);
		return $orderstatus;
	}

	// get grouplist
	public static function GroupList($selected = null, $id = '', $attribs = array(), $default_option = null) {
		$group_options [] = HTMLHelper::_( 'select.option', '', Text::_ ( 'JALL' ) );

		$groupList = HTMLHelper::_('user.groups');

		foreach ( $groupList as $row ) {
			$group_options [] = HTMLHelper::_( 'select.option', $row->value, Text::_ ( $row->text ) );
		}

		return self::genericlist ( $group_options, $id, $attribs, $selected, $id );
	}

	// get paymentlist
	public static function PaymentList($selected = null, $id = '', $attribs = array(), $default_option = null)
    {
		$paymentmethod_options [] = HTMLHelper::_( 'select.option', '', Text::_ ('JALL') );

		return self::genericlist ( $paymentmethod_options, $id, $attribs, $selected, $id );
	}

	public static function publish($name, $selected = '', $attribs = array())
	{
		$options = [];

		$options[] = HTMLHelper::_('select.option', '1'  ,Text::_('JYES'));
		$options[] = HTMLHelper::_('select.option', '0'  ,Text::_('JNO'));

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	// get countries
    public static function getCurrencies()
    {
		$options = array ();
		$enabled = 1;
		$currencies = J2Store::fof()->getModel('currencies', 'J2StoreModel')->enabled($enabled)->getList();
		foreach ( $currencies as $currency ) {
			$options [$currency->j2store_currency_id] = $currency->currency_name;
		}
		return $options;
	}

	public static function ruleFormatType($name, $selected = '', $attribs = array())
	{
		$options = [];
		$options[] = HTMLHelper::_('select.option', ''  ,Text::_('J2STORE_SELECT_OPTION'));
		$options[] = HTMLHelper::_('select.option', 'product'  ,Text::_('J2STORE_RULE_PRODUCT'));
		$options[] = HTMLHelper::_('select.option', 'discount'  ,Text::_('J2STORE_RULE_DISCOUNT'));
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function getParentOption($variant_id,$default_par_id_array,$same_option)
    {
		$model = J2Store::fof()->getModel('Options','J2StoreModel');
		//get parent
		$pa_options= $model->getList();
		//generate parent filter list
		$parent_options = [];
		$parent_options[] = Text::_('J2STORE_SELECT_PARENT_OPTION');
		if(!empty($pa_options))
		{
			foreach($pa_options as $row) {
				// parent cannot be same option so check if same option and allow
				if($row->j2store_option_id != $same_option)
					$parent_options[$row->j2store_option_id]=$row->option_name;
			}
		}
		return $parent_options;
	}

	/**
	 * Generates shipping method type list
	 *
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @returns string HTML for the radio list
	 */
	public static function shippingtype( $selected, $name = 'filter_shipping_method_type', $attribs = array('class' => 'form-select'), $idtag = null, $allowAny = false, $title = 'J2STORE_SELECT_SHIPPING_TYPE')
	{
		$list = [];
		if($allowAny) {
			$list[] =  self::option('', "- ".Text::_( $title )." -" );
		}
		require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/library/shipping.php');
		$items = J2StoreShipping::getTypes();
		foreach ($items as $item)
		{
			$list[] = HTMLHelper::_('select.option', $item->id, $item->title );
		}
		$html = self::genericlist($list, $name, $attribs, $selected, $idtag );
		return $html;
	}

	/**
	 * Generates a selectlist for shipping methods
	 *
	 * @param unknown_type $selected
	 * @param unknown_type $name
	 * @param unknown_type $attribs
	 * @param unknown_type $idtag
	 * @return unknown_type
	 */
	public static function shippingmethod( $selected, $name = 'filter_shipping_method', $attribs = array('class' => 'form-select'), $idtag = null )
	{
		$list = [];

		F0FModel::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_j2store/models' );
		$model = J2Store::fof()->getModel('Shippingmethods', 'J2StoreModel' );

		$model->setState('filter_enabled', true);
		$items = $model->getList();
		foreach (@$items as $item)
		{
			$list[] =  self::option( $item->shipping_method_id, Text::_($item->shipping_method_name));
		}
		return HTMLHelper::_('select.radiolist', $list, $name, $attribs, 'value', 'text', $selected, $idtag);
	}

	public static function taxclass($default, $name)
    {
        $attr = [];
        $attr['class']= 'form-select';
		return J2Html::select()->clearState()
		->type('genericlist')
		->name($name)
        ->attribs($attr)
		->value($default)
		->setPlaceHolders(
				array(''=>Text::_('J2STORE_SELECT_OPTION'))
		)
		->hasOne('Taxprofiles')
		->setRelations( array(
				'fields' => array (
						'key' => 'j2store_taxprofile_id',
						'name' => array('taxprofile_name')
				)
		)
		)->getHtml();
	}

	public static function geozones($default, $name)
    {
        $attr = [];
        $attr['class']= 'form-select';

		return J2Html::select()->clearState()
		->type('genericlist')
		->name($name)
        ->attribs($attr)
		->value($default)
		->setPlaceHolders(
				array(''=>Text::_('J2STORE_SELECT_OPTION'))
		)
		->hasOne('Geozones')
		->setRelations( array(
				'fields' => array (
						'key' => 'j2store_geozone_id',
						'name' => array('geozone_name')
				)
		)
		)->getHtml();
	}

	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param   string  $extension  The extension option.
	 * @param   array   $config     An array of configuration options. By default, only published and unpublished categories are returned.
	 *
	 * @return  array   Categories for the extension
	 *
	 * @since   1.6
	 */
	public static function getContentCategories()
	{
        $config = array('filter.published' => array(0, 1));
        $extension ='com_content';
        $config = (array) $config;
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
        ->select('a.id, a.title, a.level, a.parent_id')
        ->from('#__categories AS a')
        ->where('a.parent_id > 0');

        // Filter on extension.
        $query->where('extension = ' . $db->quote($extension));

        // Filter on the published state
        if (isset($config['filter.published']))
        {
            if (is_numeric($config['filter.published']))
            {
                $query->where('a.published = ' . $db->q((int) $config['filter.published']));
            }
            elseif (is_array($config['filter.published']))
            {
                $config['filter.published'] = J2Store::platform()->toInteger($config['filter.published']);
                $query->where('a.published IN (' . implode(',', $config['filter.published']) . ')');
            }
        }

        $query->order('a.lft');

        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
	}

	public static function getManufacturers()
    {
		$items =  J2Store::fof()->getModel('Manufacturers','J2StoreModel')->getItemList();
		$new_options  = [];
		$new_options[] = Text::_('J2STORE_ALL');
		foreach($items as $brand){
			$new_options[$brand->j2store_manufacturer_id] = $brand->company;
		}
		return $new_options;
	}

	public static function getOptionTypesList($name, $id, $item)
    {
		$groups = array ();

		$types = self::getOptionTypes ();
		foreach ( $types as $type_key => $typeitems ) {
			$groups [$type_key] = array ();
			$groups [$type_key] ['text'] = Text::_ ( 'J2STORE_OPTION_OPTGROUP_LABEL_' . strtoupper ( $type_key ) );
			$groups [$type_key] ['items'] = array ();
			foreach ( $typeitems as $type ) {
				$groups [$type_key] ['items'] [] = HTMLHelper::_( 'select.option', $type, Text::_( 'J2STORE_' . strtoupper ( $type ) ) );
			}
		}

		$attr = array (
				'id' => $id,
				'list.select' => $item->type,
				'list.attr' => ['class' => 'form-select']

		);
		J2Store::plugin ()->event ( 'GetOptionTypesList', array (
				$name,
				$id,
				$item,
				$groups,
				$attr
		) );
		return HTMLHelper::_( 'select.groupedlist', $groups, $name, $attr);
	}

	public static function getOptionTypes()
    {
		$types = array ();
		$choose = array ();
		$choose [] = 'select';
		$choose [] = 'radio';
		if (J2Store::isPro ()) {
			$choose [] = 'checkbox';
		}

		$types ['choose'] = $choose;

		if (J2Store::isPro ()) {
			$types ['input'] = array (
					'text',
					'textarea',
					'file'
			);
			$types ['date'] = array (
					'date',
					'time',
					'datetime'
			);
		}
		J2Store::plugin ()->event('GetOptionTypes', array (&$types));
		return $types;
	}
}
