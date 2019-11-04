<?php
namespace core\pages\grid\modules;

class GridPageDefaultModuleToolKit{
	public static function generateDialog($customPart, $moduleId){
		return "
				<div class=\"dialog\" ng-show=\"isModuleVisible('".$moduleId."')\">
				    <div class=\"panel panel-default\">
				        <div class=\"panel-heading\">
				            <select ng-model=\"changeModuleClassName\" ng-change=\"changeModule()\" ng-show=\"showModuleSelect()\">
				                <option ng-repeat=\"module in modules\" value=\"{{module.className}}\">{{module.name}}</option>
				            </select>
				        </div>
				        <div class=\"panel-body\">
				            ".$customPart."
				        </div>
				        <!-- TODO metadata -->
				        <div class=\"panel-footer\">
				            <button ng-click=\"closeDialog()\">ok</button>
				        </div>
				    </div>
				</div>
				";
	}
}