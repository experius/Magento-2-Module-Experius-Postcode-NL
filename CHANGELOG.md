## 1.11.2 (2023-10-30)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.11.2)

*  [BUGFIX][IN23-254] Avoid using closing slash with void tags. *(Boris van Katwijk)*


## 1.11.1 (2023-07-12)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.11.1)

*  Update LicenceCheck.php *(mhaagen85)*


## 1.11.0 (2022-10-17)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.11.0)

*  [BUGFIX][PWAI-716] Avoid unused variables; see details below. *(Boris van Katwijk)*


## 1.10.0 (2022-09-06)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.10.0)

*  Changed function because ReadOnly Class can not be used in PHP8.1 *(Demi Holland)*
*  Edit trim function because PHP8.1 compatibility *(Demi Holland)*


## 1.9.0 (2021-07-15)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.9.0)

*  [FEATURE][DRRS-47] - Updated postcode api url with new url to keep continued support *(Rens Wolters)*


## 1.8.2 (2021-05-12)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.8.2)

*  [BUGFIX] - Fix housenumber addition *(Ruben Panis)*


## 1.8.1 (2021-02-01)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.8.1)

*  [BUGFIX] Solved "TypeError: Cannot read property 'get' of undefined" when country is set to Belgium *(Mr. Lewis)*


## 1.8.0 (2020-10-28)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.8.0)

*  [FEATURE] Introduce pluggable function to alter sort order of the postcode fields for different checkouts than the default Magento OnePage. *(Boris van Katwijk)*


## 1.7.0 (2020-10-05)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.7.0)

*  [FEATURE]Added pattern to housenumber input for numeric keyboard on ios *(joeydankbaar)*


## 1.6.0 (2020-10-02)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.6.0)

*  [DOCS] Updated the CHANGELOG.md *(Lewis Voncken)*
*  [FEATURE] Split GraphQl functionality from module *(René Schep)*


## 1.5.0 (2020-10-01)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.5.0)

*  [FEATURE] Changed the template for the housenumber to set input type number *(Lewis Voncken)*


## 1.4.2 (2020-09-30)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.4.2)

*  Update URLs, services.postcode.nl no longer exists *(SjonHortensius)*
*  Prevent javascript error by adding a warning comment when you want to use seperate address fields *(joeydankbaar)*
*  [BUGFIX] Fixed issue where street gets [object] in name. *(Egor Dmitriev)*
*  [BUGFIX] Fixed issue where street gets duplicate values while changing housenumber, additon or on initial load *(Egor Dmitriev)*


## 1.4.1 (2020-09-22)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.4.1)

*  [FEATURE] Updated GraphQl Exception type for lookupAddress errors *(René Schep)*


## 1.4.0 (2020-09-15)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.4.0)

*  [DOC] Cleaned some code *(René Schep)*
*  [FEATURE] Added support for GraphQl *(René Schep)*
*  [FEATURE] Added magento version restriction *(René Schep)*
*  [BUGFIX] Typo *(René Schep)*
*  [BUGFIX] Fixed version restraint *(René Schep)*


## 1.3.4 (2020-04-09)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.3.4)

*  [BUGFIX] removed region_id set, expects an int not a string. *(Experius)*


## 1.3.3 (2019-11-27)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.3.3)

*  Prevent notice showing undefined *(jordy2607)*


## 1.3.2 (2019-11-21)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.3.2)

*  [BUGFIX] solved invalid width for the input fields in the checkout *(Mr. Lewis)*


## 1.3.1 (2019-07-01)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.3.1)

*  [TASK] - Change echo json_encode data to return resultJsonFactory as to prevent pipeline error *(Cas Satter)*


## 1.3.0 (2019-01-29)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.3.0)

*  Typo *(Michiel Gerritsen)*
*  [TASK] Added Api keycheck in backend and system message when key is invalid *(Matthijs Breed)*
*  [BUGFIX] Fixed checkout postcode validation in checkout form to fill in magento housenumber field when user switches to manual input *(Matthijs Breed)*
*  [TASK] Improved readability of javascript *(Hexmage)*
*  [TASK] Fixed formatting *(Hexmage)*
*  Fixed Formatting *(Hexmage)*
*  PSR-2 Formatting *(Hexmage)*
*  PSR-2 Formatting *(Hexmage)*
*  PSR-2 Formatting *(Hexmage)*
*  Fixed Formatting *(Hexmage)*
*  PSR-2 Formatting *(Hexmage)*


## 1.2.1 (2018-08-15)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.2.1)

*  [BUGFIX] Fixed Housenumber addition not being seperated to next street field *(René Schep)*


## 1.2.0 (2018-03-13)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.2.0)

*  [TASK] Updated the Styling in the Checkout and Added a notice with 'Fill in your Shipping Address below:' *(Lewis Voncken)*


## 1.1.6 (2018-03-13)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.1.6)

*  [TASK] Added canRestore to the configuration fields *(Lewis Voncken)*


## 1.1.5 (2018-03-13)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.1.5)

*  [BUGFIX] [issue#29] Solved Problem with Address showing [object Object] on checkout Startup *(Lewis Voncken)*


## 1.1.4 (2017-12-07)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.1.4)

*  [BUGFIX] Postcode now written to shipping address object *(jessetaverne)*


## 1.1.3 (2017-11-02)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.1.3)

*  [TASK] Make module pass PHPstan checks [TASK] Make module PSR-2 Compliant *(Bart Lubbersen)*
*  [TASK] Solved code complexity problems *(Bart Lubbersen)*
*  [TASK] Solved codesniffer feedback *(Bart Lubbersen)*
*  [BUGFIX] Solved problems with sometimes not displaying the correct fields because of registry delays [BUGFIX] Solved issue for orders without shipments (virtual and downloadable) *(Bart Lubbersen)*
*  [BUGFIX] Solved DI compile errors since previous commits *(Bart Lubbersen)*


## 1.1.2 (2017-09-04)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.1.2)

*  [BUGFIX] Solved problem with address addition being hidden if mode is manual *(Bart Lubbersen)*


## 1.1.1 (2017-09-01)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.1.1)

*  [BUGFIX] Solved problem with classes not being set and removed correctly because additionalClasses is only set upon first time loading *(Bart Lubbersen)*


## 1.1.0 (2017-08-31)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.1.0)

*  [FIX] - if addres has an addition: first value to select is not 'false' but No housenumber addition *(Rakhal)*
*  [TASK] Changed the way the addresses are filled, now not in textblock but address fields themselves are shown disabled [TASK] Added validation required for postcode module specific fields if enabled [TASK] Added validation of postcode validation upon submitting form [TASK] Moved styling to less files instead of inside html files *(Bart Lubbersen)*


## 1.0.13 (2017-07-28)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.13)

*  hideFields fix *(jordy2607)*


## 1.0.12 (2017-07-25)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.12)

*  this.source is undefined in load *(jordy2607)*


## 1.0.11 (2017-07-07)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.11)

*  [TASK] Add NL translations *(Bart Lubbersen)*
*  [BUGFIX] Debug mode was not checked from right config value *(Bart Lubbersen)*
*  [TASK] Corrected sort order of address fields *(Bart Lubbersen)*
*  [BUGFIX] Correctly hide all address fields on first checkout load [BUGFIX] Don't hide and show region field cause Magento module should handle this and module currently can only be used in the Netherlands where region is not required anyway *(Bart Lubbersen)*


## 1.0.10 (2017-07-06)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.10)

*  Region_id field fix + hide preview field notice after country change *(jordy2607)*


## 1.0.9 (2017-07-03)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.9)

*  [BUGFIX] #13 Fixed strange behaviour with addition with whitespace. Removed whitespace *(Derrick Heesbeen)*


## 1.0.8 (2017-07-03)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.8)

*  [BUGFIX] #16 Region field fix, #11 sort order field fix, #10 street hide fix *(Derrick Heesbeen)*


## 1.0.7 (2017-06-30)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.7)

*  Get storeconfig for use_street2_as_housenumber, not configurable in backend in magento *(Rakhal)*
*  [BUGFIX] layout fix for templates that enherit from blank them *(Derrick Heesbeen)*


## 1.0.6 (2017-05-01)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.6)

*  [BUGFIX] issue #10 Housenumbers not showing, cast to string *(Derrick Heesbeen)*


## 1.0.5 (2017-05-01)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.5)

*  [FEATURE] Start with postcode validator in customer address *(Derrick Heesbeen)*
*  [BUGFIX] compile error fix. Duplicate depedencies *(Derrick Heesbeen)*
*  [BUGFIX] Apply fieldset to billingaddress *(Derrick Heesbeen)*
*  [BUGFIX] disable customer address implementation *(Derrick Heesbeen)*


## 1.0.4 (2017-04-07)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.4)

*  Refactor and added loader *(Derrick Heesbeen)*


## 1.0.3 (2017-03-20)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.3)

*  Country field listener show/hide *(Derrick Heesbeen)*


## 1.0.2 (2017-01-02)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.2)

*  Removed Plugin, Add layout processor *(Derrick Heesbeen)*
*  Module Sequence, Layoutprocessor checks *(Derrick Heesbeen)*
*  Generated Translation file, addition bugfixes, fixed settings *(Derrick Heesbeen)*


## 1.0.1 (2016-10-18)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.1)

*  Changed settings, add default, add some config paths *(Derrick Heesbeen)*


## 1.0.0 (2016-09-29)

[View Release](git@github.com:experius/Magento-2-Module-Experius-Postcode-NL.git/commits/tag/1.0.0)

*  First commit *(Derrick Heesbeen)*
*  Added postcode and housenumber field to shipping and billing forms, hide fields, fill fields, start with setting *(Derrick Heesbeen)*
*  added Readme" *(Derrick Heesbeen)*
*  [FEATURE] Changed old config path *(Derrick Heesbeen)*
*  change system.xml configuration *(Derrick Heesbeen)*
*  add license *(Derrick Heesbeen)*
*  add disable checkbox and addition field *(Derrick Heesbeen)*
*  made js setting dynamic *(Derrick Heesbeen)*
*  [FEATURE] write the addition value to streetline *(Derrick Heesbeen)*
*  [TASK] Add module prefix to module like Magento does *(Bart Lubbersen)*


