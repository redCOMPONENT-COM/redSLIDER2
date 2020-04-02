<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2016 - 2020 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$data = $displayData;

// Load the form list fields
$list = $data['view']->filterForm->getGroup('list');

Factory::getDocument()->addScriptDeclaration("	
    jQuery(document).ready(function () {
         jQuery('.showWarningMsg').each(function(){
            var limitSelect = jQuery(this);          
            var preVal = limitSelect.val();
            limitSelect.bind('change', function(){
                
                if(jQuery(this).val() == '0')
                {
                    if(confirm('" . Text::_('COM_REDSLIDER_LIMIT_ALL_VALUE_WARNING') . "'))
                    {
                        jQuery(this).closest('form#adminForm').submit();
                    }
                    else
                    {
                        jQuery(this).val(preVal).trigger('liszt:updated').trigger('chosen:updated');
                         e.preventDefault();                        
                    }
                }else{
                    jQuery(this).closest('form#adminForm').submit();
                }           
            });
         });
    });"
);

?>
<ul class="ordering-select list-inline">
	<?php foreach ($list as $fieldName => $field) : ?>
		<li class="js-stools-field-list">
			<?php echo $field->input; ?>
		</li>
	<?php endforeach; ?>
</ul>
