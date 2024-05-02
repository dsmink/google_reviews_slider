## The Google Reviews module provides the following functionalities : 

The Google Reviews module provides the following functionalities : 

1. Import reviews into a content type
This module imports the Google reviews of one or multiple places defined in the settings accessed via the Review Settings menu.
Imports are triggered by a cron and the fetch reviews button.

2. Display review blocks
This module contains a custom block to display the global rating and a defined number of review messages under the global review.
The block displays the following informations : 
- A title message (optionnal)
- The global review (optionnal)
- The message of the review
- The name of the reviewer
- The rating of the review displayed in stars
- The time since the review was written
- The profile picture of the reviewer
- A link to the place to leave a review (optionnal)

3. Configurable module
The settings contain the following options : 
- The minimum rating to display
- The number of messages to display in the review block
- The maximum duration of the reviews to display
- The title displayed above the review block
- Wether or not display the global review
- The link where people should leave a review
- The text of the review page link

## Setup steps :

### 1. Install the module
1. Go to [ Manage -> Extend -> List ] 
2. Scroll down to the Custom section.
3. Check the Google Reviews option checkbox.
4. Click on install at the bottom of the page.
5. The module will be loaded and a confirmation will appear.

### 2. Configure the module
Go to [ Configuration -> Web services -> Review Settings ] to fill the configuration fields.
1. API key : The Google API Key to have access to the Google Places API.
2. The Place IDs of the reviews : Add one or more Place Ids to display the reviews of these places.
3. The minimum rating to display : The reviews with a rating under this value will no be shown on the reviews block. They will still count in the global review.
4. Link to leave a review (Optionnal) : Adds a link at the bottom of the block to the page where people can leave reviews.
5. The text of the review page link (Optionnal)

### 3. Add the custom block
1. Go to [ Structure -> Block Layout ]
2. Click on the Place block button of the section where you want to add the Google Reviews Block.
3. Scroll down the block list and click on Place block next to the Google reviews content block option.
4. Configure the default options and save the block.
