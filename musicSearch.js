// JavaScript Document

var musicSearch = { }

/*
	All of these properties correspond to filters on the page
	They keep track of things like the number of tags users have created for a filter, 
	the current ID of a tag, and arrays that hold the data used to render HTML and generate SQL
*/

musicSearch.PubColonyTagID = 0;
musicSearch.PubColonyTagCount = 0;
musicSearch.PubColonyTags = new Array();
musicSearch.PubColonyNames = new Array();
musicSearch.PubColonyNames["CT"] = "Conneticut";
musicSearch.PubColonyNames["FL"] = "Florida";
musicSearch.PubColonyNames["GA"] = "Georgia";
musicSearch.PubColonyNames["MA"] = "Massachusetts";
musicSearch.PubColonyNames["MD"] = "Maryland";
musicSearch.PubColonyNames["NC"] = "North Carolina";
musicSearch.PubColonyNames["NH"] = "New Hampshire";
musicSearch.PubColonyNames["NJ"] = "New Jeresey";
musicSearch.PubColonyNames["NY"] = "New York";
musicSearch.PubColonyNames["PA"] = "Pennsylvania";
musicSearch.PubColonyNames["RI"] = "Rhode Island";
musicSearch.PubColonyNames["SC"] = "South Carolina";
musicSearch.PubColonyNames["UK"] = "United Kingdom";
musicSearch.PubColonyNames["VA"] = "Virginia";
musicSearch.PubColonyNames["VT"] = "Vermont";
musicSearch.possiblePubColony = null;

musicSearch.PubCityTagID = 0;
musicSearch.PubCityTagCount = 0;
musicSearch.PubCityTags = new Array();
musicSearch.possiblePubCity = null;

musicSearch.YearTagID = 0;
musicSearch.YearTagCount = 0;

musicSearch.searchTags = new Array();
musicSearch.numTags = musicSearch.searchTags.length;

musicSearch.possibleLocation = null;
musicSearch.LocationTagID = 0;
musicSearch.LocationTagCount = 0;
musicSearch.LocationTags = new Array();

musicSearch.LocationCodesTagID = 0;
musicSearch.LocationCodesTagCount = 0;
musicSearch.LocationCodesTags = new Array();

musicSearch.TitleTagID = 0;
musicSearch.TitleTagCount = 0;
musicSearch.TitleTags = new Array();
musicSearch.possibleTitle = null;

musicSearch.ContentTagID = 0;
musicSearch.ContentTagCount = 0;

musicSearch.searchTag = {
	
	id: "*",
	index: -1,
	data_content: "*", 	/* only used for Content */
	data_andor: "*",
	data_like: "*",
	data_value: "*",
	data_name: "*", 	/* only used for LocationCodes */
	
	serialize: function() {
		return this.id+")|("+this.index+")|("+this.data_content+")|("+this.data_andor+")|("+this.data_like+")|("+this.data_value+")|("+this.data_name;	
	}
}

musicSearch.allSearchTags = [];
