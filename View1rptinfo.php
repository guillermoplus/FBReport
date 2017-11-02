<?php

// Global variable for table object
$View1 = NULL;

//
// Table class for View1
//
class crView1 extends crTableBase {
	var $ShowGroupHeaderAsRow = FALSE;
	var $ShowCompactSummaryFooter = TRUE;
	var $product_s_desc;
	var $product_desc;
	var $product_name;
	var $slug;
	var $product_sku;
	var $virtuemart_product_id;
	var $product_url;
	var $category_name;
	var $file_url;
	var $file_type;
	var $product_price;
	var $mf_name;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage, $gsLanguage;
		$this->TableVar = 'View1';
		$this->TableName = 'View1';
		$this->TableType = 'VIEW';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// product_s_desc
		$this->product_s_desc = new crField('View1', 'View1', 'x_product_s_desc', 'product_s_desc', '`product_s_desc`', 201, EWR_DATATYPE_MEMO, -1);
		$this->product_s_desc->Sortable = TRUE; // Allow sort
		$this->fields['product_s_desc'] = &$this->product_s_desc;
		$this->product_s_desc->DateFilter = "";
		$this->product_s_desc->SqlSelect = "";
		$this->product_s_desc->SqlOrderBy = "";

		// product_desc
		$this->product_desc = new crField('View1', 'View1', 'x_product_desc', 'product_desc', '`product_desc`', 201, EWR_DATATYPE_MEMO, -1);
		$this->product_desc->Sortable = TRUE; // Allow sort
		$this->fields['product_desc'] = &$this->product_desc;
		$this->product_desc->DateFilter = "";
		$this->product_desc->SqlSelect = "";
		$this->product_desc->SqlOrderBy = "";

		// product_name
		$this->product_name = new crField('View1', 'View1', 'x_product_name', 'product_name', '`product_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->product_name->Sortable = TRUE; // Allow sort
		$this->fields['product_name'] = &$this->product_name;
		$this->product_name->DateFilter = "";
		$this->product_name->SqlSelect = "";
		$this->product_name->SqlOrderBy = "";

		// slug
		$this->slug = new crField('View1', 'View1', 'x_slug', 'slug', '`slug`', 200, EWR_DATATYPE_STRING, -1);
		$this->slug->Sortable = TRUE; // Allow sort
		$this->fields['slug'] = &$this->slug;
		$this->slug->DateFilter = "";
		$this->slug->SqlSelect = "";
		$this->slug->SqlOrderBy = "";

		// product_sku
		$this->product_sku = new crField('View1', 'View1', 'x_product_sku', 'product_sku', '`product_sku`', 200, EWR_DATATYPE_STRING, -1);
		$this->product_sku->Sortable = TRUE; // Allow sort
		$this->fields['product_sku'] = &$this->product_sku;
		$this->product_sku->DateFilter = "";
		$this->product_sku->SqlSelect = "";
		$this->product_sku->SqlOrderBy = "";

		// virtuemart_product_id
		$this->virtuemart_product_id = new crField('View1', 'View1', 'x_virtuemart_product_id', 'virtuemart_product_id', '`virtuemart_product_id`', 19, EWR_DATATYPE_NUMBER, -1);
		$this->virtuemart_product_id->Sortable = TRUE; // Allow sort
		$this->virtuemart_product_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['virtuemart_product_id'] = &$this->virtuemart_product_id;
		$this->virtuemart_product_id->DateFilter = "";
		$this->virtuemart_product_id->SqlSelect = "";
		$this->virtuemart_product_id->SqlOrderBy = "";

		// product_url
		$this->product_url = new crField('View1', 'View1', 'x_product_url', 'product_url', '`product_url`', 200, EWR_DATATYPE_STRING, -1);
		$this->product_url->Sortable = TRUE; // Allow sort
		$this->fields['product_url'] = &$this->product_url;
		$this->product_url->DateFilter = "";
		$this->product_url->SqlSelect = "";
		$this->product_url->SqlOrderBy = "";

		// category_name
		$this->category_name = new crField('View1', 'View1', 'x_category_name', 'category_name', '`category_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->category_name->Sortable = TRUE; // Allow sort
		$this->fields['category_name'] = &$this->category_name;
		$this->category_name->DateFilter = "";
		$this->category_name->SqlSelect = "";
		$this->category_name->SqlOrderBy = "";

		// file_url
		$this->file_url = new crField('View1', 'View1', 'x_file_url', 'file_url', '`file_url`', 201, EWR_DATATYPE_MEMO, -1);
		$this->file_url->Sortable = TRUE; // Allow sort
		$this->fields['file_url'] = &$this->file_url;
		$this->file_url->DateFilter = "";
		$this->file_url->SqlSelect = "";
		$this->file_url->SqlOrderBy = "";

		// file_type
		$this->file_type = new crField('View1', 'View1', 'x_file_type', 'file_type', '`file_type`', 200, EWR_DATATYPE_STRING, -1);
		$this->file_type->Sortable = TRUE; // Allow sort
		$this->fields['file_type'] = &$this->file_type;
		$this->file_type->DateFilter = "";
		$this->file_type->SqlSelect = "";
		$this->file_type->SqlOrderBy = "";

		// product_price
		$this->product_price = new crField('View1', 'View1', 'x_product_price', 'product_price', '`product_price`', 131, EWR_DATATYPE_NUMBER, -1);
		$this->product_price->Sortable = TRUE; // Allow sort
		$this->product_price->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['product_price'] = &$this->product_price;
		$this->product_price->DateFilter = "";
		$this->product_price->SqlSelect = "";
		$this->product_price->SqlOrderBy = "";

		// mf_name
		$this->mf_name = new crField('View1', 'View1', 'x_mf_name', 'mf_name', '`mf_name`', 200, EWR_DATATYPE_STRING, -1);
		$this->mf_name->Sortable = TRUE; // Allow sort
		$this->fields['mf_name'] = &$this->mf_name;
		$this->mf_name->DateFilter = "";
		$this->mf_name->SqlSelect = "";
		$this->mf_name->SqlOrderBy = "";
	}

	// Set Field Visibility
	function SetFieldVisibility($fldparm) {
		global $Security;
		return $this->$fldparm->Visible; // Returns original value
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			if ($ofld->GroupingFieldId == 0)
				$this->setDetailOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			if ($ofld->GroupingFieldId == 0) $ofld->setSort("");
		}
	}

	// Get Sort SQL
	function SortSql() {
		$sDtlSortSql = $this->getDetailOrderBy(); // Get ORDER BY for detail fields from session
		$argrps = array();
		foreach ($this->fields as $fld) {
			if ($fld->getSort() <> "") {
				$fldsql = $fld->FldExpression;
				if ($fld->GroupingFieldId > 0) {
					if ($fld->FldGroupSql <> "")
						$argrps[$fld->GroupingFieldId] = str_replace("%s", $fldsql, $fld->FldGroupSql) . " " . $fld->getSort();
					else
						$argrps[$fld->GroupingFieldId] = $fldsql . " " . $fld->getSort();
				}
			}
		}
		$sSortSql = "";
		foreach ($argrps as $grp) {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $grp;
		}
		if ($sDtlSortSql <> "") {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $sDtlSortSql;
		}
		return $sSortSql;
	}

	// Table level SQL
	// From

	var $_SqlFrom = "";

	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`View1`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}

	// Select
	var $_SqlSelect = "";

	function getSqlSelect() {
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

	// Where
	var $_SqlWhere = "";

	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}

	// Group By
	var $_SqlGroupBy = "";

	function getSqlGroupBy() {
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}

	// Having
	var $_SqlHaving = "";

	function getSqlHaving() {
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}

	// Order By
	var $_SqlOrderBy = "";

	function getSqlOrderBy() {
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}

	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Aggregate Prefix
	var $_SqlAggPfx = "";

	function getSqlAggPfx() {
		return ($this->_SqlAggPfx <> "") ? $this->_SqlAggPfx : "";
	}

	function SqlAggPfx() { // For backward compatibility
		return $this->getSqlAggPfx();
	}

	function setSqlAggPfx($v) {
		$this->_SqlAggPfx = $v;
	}

	// Aggregate Suffix
	var $_SqlAggSfx = "";

	function getSqlAggSfx() {
		return ($this->_SqlAggSfx <> "") ? $this->_SqlAggSfx : "";
	}

	function SqlAggSfx() { // For backward compatibility
		return $this->getSqlAggSfx();
	}

	function setSqlAggSfx($v) {
		$this->_SqlAggSfx = $v;
	}

	// Select Count
	var $_SqlSelectCount = "";

	function getSqlSelectCount() {
		return ($this->_SqlSelectCount <> "") ? $this->_SqlSelectCount : "SELECT COUNT(*) FROM " . $this->getSqlFrom();
	}

	function SqlSelectCount() { // For backward compatibility
		return $this->getSqlSelectCount();
	}

	function setSqlSelectCount($v) {
		$this->_SqlSelectCount = $v;
	}

	// Sort URL
	function SortUrl(&$fld) {
		return "";
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
		}
	}

	// Table level events
	// Page Selecting event
	function Page_Selecting(&$filter) {

		// Enter your code here
	}

	// Page Breaking event
	function Page_Breaking(&$break, &$content) {

		// Example:
		//$break = FALSE; // Skip page break, or
		//$content = "<div style=\"page-break-after:always;\">&nbsp;</div>"; // Modify page break content

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here
	}

	// Cell Rendered event
	function Cell_Rendered(&$Field, $CurrentValue, &$ViewValue, &$ViewAttrs, &$CellAttrs, &$HrefValue, &$LinkAttrs) {

		//$ViewValue = "xxx";
		//$ViewAttrs["style"] = "xxx";

	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>);

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}

	// Load Filters event
	function Page_FilterLoad() {

		// Enter your code here
		// Example: Register/Unregister Custom Extended Filter
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A', 'GetStartsWithAFilter'); // With function, or
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A'); // No function, use Page_Filtering event
		//ewr_UnregisterFilter($this-><Field>, 'StartsWithA');

	}

	// Page Filter Validated event
	function Page_FilterValidated() {

		// Example:
		//$this->MyField1->SearchValue = "your search criteria"; // Search value

	}

	// Page Filtering event
	function Page_Filtering(&$fld, &$filter, $typ, $opr = "", $val = "", $cond = "", $opr2 = "", $val2 = "") {

		// Note: ALWAYS CHECK THE FILTER TYPE ($typ)! Example:
		//if ($typ == "dropdown" && $fld->FldName == "MyField") // Dropdown filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "extended" && $fld->FldName == "MyField") // Extended filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "popup" && $fld->FldName == "MyField") // Popup filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "custom" && $opr == "..." && $fld->FldName == "MyField") // Custom filter, $opr is the custom filter ID
		//	$filter = "..."; // Modify the filter

	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}
}
?>
