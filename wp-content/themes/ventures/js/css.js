function getCSSRuleIndex(sheet, selector) {
	var i = 0;
	var scRule = selector.toLowerCase().replace('::before', ':before').replace('::after', ':after');
	var dcRule = scRule.replace(':before', '::before').replace(':after', '::after');
	var cssRule = false;
	do {
		cssRule = (sheet.cssRules) ? sheet.cssRules[i] : sheet.rules[i];
		if (cssRule) {
			var s = cssRule.selectorText.toLowerCase();
			if (s === scRule || s === dcRule) {
				return i;
			}
			i++;
		}
	} while (cssRule);
	return false;
}

function getCSSRuleByIndex(sheet, i) {
	return (sheet.cssRules) ? sheet.cssRules[i] : sheet.rules[i];
}

function addCSSRule(sheet, selector, rule) {
	var rules = sheet.cssRules || sheet.rules;
	if ('insertRule' in sheet) {
		sheet.insertRule(selector+'{'+rule+'}', 0);
	}
	else if("addRule" in sheet) {
		sheet.addRule(selector, rule, -1);
	}
}

function deleteCSSRule(sheet, selector) {
	var i = false;
	do {
		i = getCSSRuleIndex(sheet, selector);
		if (i === false) return;
		if (sheet.cssRules) {
			return sheet.deleteRule(i);
		}
		return sheet.removeRule(i);
	} while(i);
}

function replaceCSSRule(sheet, selector, rule) {
	deleteCSSRule(sheet, selector);
	addCSSRule(sheet, selector, rule);
}