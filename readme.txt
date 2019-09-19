=== ZodiacPress ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R7BHLMCQ437SS
Tags: zodiacpress, zodiac, astrology, horoscope, natal report, birth report, birth chart, astrology reports, sidereal
Requires at least: 4.7
Tested up to: 5.2.3
Requires PHP: 5.6
Stable tag: 2.0.2
License: GNU GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate astrology birth reports with your custom interpretations.

== Description ==

ZodiacPress is the first WordPress plugin that lets you generate astrology birth reports with your custom interpretations, directly on your site. 

This is **not** an embedded script that pulls astrology data from another astrology site. ZodiacPress turns your site into an astrology report press. Your astrology interpretations reside on your own site, and the reports are created on your own site. The Swiss Ephemeris is included inside. Also includes Sidereal zodiac options.

The birth report includes three parts: 

1. Planets and Points in The Signs
2. Planets and Points in The Houses
3. Aspects

= Birth Report Details =

You can choose which planets and aspects to include in the birth report.

You can choose to add a chart wheel drawing to the report.

Tropical zodiac is the default, but you can choose to use the Sidereal Zodiac. Choose from 4 sidereal methods: Hindu/Lahiri, Fagan/Bradley, Raman, or Krishnamurti.

You can set a house system to be used for the report. The default is Placidus, but you can choose from 12 house systems.

You can add an optional Intro and Closing to the birth report.

You have the option to allow people with unknown birth times to generate basic natal reports. These reports with unknown times would omit time-sensitive points (i.e. Moon, Ascendant, etc.) and the Houses section.

The Planets in Houses section of the report will tell you if you have a planet in one house, but conjunct the next house (within 1.5 degrees orb; orb can be modified with a filter).

You get granular control over aspect orbs. It lets you assign different orbs for each planet and each type of aspect.

If birth time is unknown, ZP checks for ingress on that day rather than simply using the planet's noon position. If an ingress occurs at any time on that day, it lets the person know that the planet changed signs on that day, and from which sign to which it changed.

= Interpretations Are Optional =

Entering your interpretations is not required since you can generate reports without interpretations text. See the [screenshots](https://wordpress.org/plugins/zodiacpress/screenshots/) to see how a basic report **without** interpretations text looks.

= Privacy Policy and EU GDPR Information =

ZodiacPress does not store the data that is entered into the form. Once a user submits the form with their birth information, that data is used instantaneously to generate a report, and then the data is lost. It is not saved with cookies or to any database. **So, you can confidently add a notice somewhere on your page (whether under the form, or on the footer of your site) to inform your users that the Birth Report form is not storing their data.**

= Technical Details =

ZodiacPress gets birth place latitude/longitude coordinates from the GeoNames geographical database which uses the latest revision of World Geodetic System (WGS 84).

ZP uses the Swiss Ephemeris (under GNU GPLv2) to get the longitude of the planets/celestial bodies. This ephemeris is included inside the plugin.

= Internationalization =

Much effort has been made to internationalize even the digits (numbers, years, and other integers in the plugin). On the birth report form, the month and day fields will switch places according to your date settings. Suggestions regarding i18n are welcome.

= Languages =

If you want to translate this plugin to your language, please see [ZodiacPress in Your Language](https://isabelcastillo.com/docs/zodiacpress-language).

= Contributing =

Anyone is welcome to contribute to ZodiacPress. Please read the [guidelines for contributing](https://github.com/isabelc/zodiacpress/blob/master/CONTRIBUTING.md) to this repository.

There are various ways you can contribute:

1. Raise an [Issue](https://github.com/isabelc/zodiacpress/issues) on GitHub.
2. Send us a Pull Request with your bug fixes and/or new features.
3. Translate ZodiacPress into [different languages](https://isabelcastillo.com/docs/zodiacpress-language).
4. Provide feedback and suggestions on [enhancements](https://github.com/isabelc/zodiacpress/issues?q=is%3Aissue+is%3Aopen+label%3Aenhancement).

See the full [ZodiacPress documentation](https://isabelcastillo.com/docs/category/zodiacpress "ZodiacPress documentation").

== Installation ==

**Install and Activate**

1. Install and activate the plugin in your WordPress dashboard by going to Plugins –> Add New. 
2. Search for “ZodiacPress” to find the plugin.
3. When you see ZodiacPress, click “Install Now” to install the plugin.
4. Click “Activate” to activate the plugin.

**Quick Setup**

The [Quick Start Guide](https://isabelcastillo.com/docs/quick-start-guide) is the fastest to way to get the ZodiacPress birth report working on your site. This allows you to generate a basic report which lists the planets in the signs, planets in the houses, and aspects.

Interpretations will not be included in the report until you enter your own natal interpretations. To enter your interpretations, go to “ZodiacPress” in your dashboard menu. See the [Full Setup Guide](https://isabelcastillo.com/docs/full-setup-guide "ZodiacPress Documentation") for important options.

**If your website uses Windows hosting**

If your website is running on a Windows operating system (i.e. using Windows hosting), then you'll need to use the [ZodiacPress Windows Server](https://isabelcastillo.com/free-plugins/zodiacpress-windows-server) plugin to make the Ephemeris work on your server. This is because the ephemeris included in ZodiacPress will not run on Windows, by default. Just install and activate the “ZodiacPress Windows Server” plugin, and it will automatically solve this problem.

== Frequently Asked Questions ==

= What if I need custom work? =

Due to the high number of customization requests that I receive, I, unfortunately, would not be able to service them even if I wanted to.

This plugin is provided as is. It is created, supported, and enhanced entirely through volunteer hours which are sometimes very limited. While I will do my best to help you to configure ZodiacPress to work for you, I cannot offer customization work. You can post your request for customization on the support forum because sometimes other members in the community may be insterested in a similar customziation and can help provide a solution.

= Why is the birth report not working? =

Please do the <a href="https://isabelcastillo.com/docs/pre-support-self-check">Pre-Support Self-Check</a> to solve common issues. You can also browse these [troubleshooting articles](https://isabelcastillo.com/docs/category/zodiacpress/troubleshooting-zodiacpress "Troubleshooting ZodiacPress").

= Do I have to create a GeoNames account? =

When your visitor enters a birth city into the report form, the plugin will get the latitude/longitude coordinates of that birth city from GeoNames. GeoNames provides this as a free web service. GeoNames requires you to have an account on their site in order to use their web service.

You do **NOT** have to create a GeoNames account if you use your own atlas database, which the [ZP Atlas](https://isabelcastillo.com/free-plugins/zpatlas) plugin helps you to do.

= How can I set the house system to be used for the "Planets in Houses" section of the report? =

The Placidus House System is used by default. To change the house system, you can either use the [ZP House Systems](https://isabelcastillo.com/free-plugins/zodiacpress-house-systems "ZP House Systems") extension, or [set the house system](https://isabelcastillo.com/docs/choose-house-system) directly in the shortcode.

= How can I give back? =

Please [rate](https://wordpress.org/support/plugin/zodiacpress/reviews/) the plugin. Thank you.

== Screenshots ==

1. This is how the Planets in Signs part of the report will look with interpretations.
2. This is how the Planets in Signs part of the report looks if you don't enter any interpretations.
3. This is how the Planets in Houses will look with interpretations.
4. This is how the Planets in Houses looks if you don't enter any interpretations.
5. This is how the Aspects section of the report will look with interpretations.
6. This is how the Aspects section looks if you don't enter any interpretations.
7. The ZodiacPress admin page where you enter and save your custom natal interpretations
8. The form to generate a birth report. The month and day fields will switch places according to your local date settings.
 
== Changelog ==

= 2.0.2 =
* Tweak - Allow asterisk to be removed with Unknown Birth Time Note on the form, by including the asterisk in the zp_unknown_birth_time_checkbox filter.
* Tweak - Pass the report variation to the zp_setup_chart action.

= 2.0.1 =
* New - Remove obsolete fonts file types. This has the added benefit of reducing the overall size of the plugin.
* Tweak - Better scrollTo distance when the report appears.
* Tweak - The Submit button text is no longer uppercase by default.
* Build - removed the deprecated form_title arg from the shortcode arguments.
* Build - Removed the deprecated function zp_geonames_js_strings.

= 2.0 =
* New - Several possibly breaking changes, so please read the [release notes](https://isabelcastillo.com/zp-release-2) before updating.
* New - The minimum required version of PHP is now 5.6.
* New - Exported the Atlas database option to a new plugin. If you use your own Atlas Database instead of GeoNames, then you must install the ZP Atlas plugin BEFORE updating to ZodiacPress 2.0. See the [release notes](https://isabelcastillo.com/zp-release-2) for details and steps.
* New - Removed all front end jquery dependency, including jquery-autocomplete for a much, much, much lighter footprint!
* New - Removed the form title, which is the title that is displayed to the user above the birth report form. 
* New - Removed the zp_shortcode_default_form_title filter.
* New - Moved the ZP System Info from the ZP Tools page to the new WordPress Site Health Info page.
* New - Synced both JavaScript files into 1, just zp.js. There is no longer an additional zp-autocomplete.js.
* Tweak - Fixed the max width on the birth report form so that it fits much better on mobile phones and small devices.
* Tweak - Changed the ZP admin buttons on the top-right corner into regular links.
* Build - Added a shorten option to the shortcode. This will allow developers to provide short previews of the report instead of the full interpretations.
* Build - Removed zp_add_cron_schedule filter.
* Build - Removed deprecated functions and the file back-compat.php.
* Build - Removed the old plugin Updater and licensing functions and files, and license settings.
* Build - For the GeoNames search request, changed dataType: jsonp to json.

= 1.9.1 =
* Fix - Removed extra slash from URL for add-on updates.
* Build - Add skip_place to zp_validate_form.

= 1.9 =
* New - The form accepts birth years up to 2020.
* New - The 'Only Chart Wheel' form will now honor its own setting for "Allow unknown birth time." Previously, the "Only Chart Wheel" form was not honoring this setting.
* New - Removed all license stuff for extension licenses, including removing license fields from the settings.
* Fix - Get offset after unknown_time is checked on the order form. Previously, if they forgot to check the box for unknown time, and they checked it after the reminder appeared, then the form would freeze up. This is fixed.
* Tweak - Update admin CSS styles, padding around boxes, to keep up with WP changes.
* Build - Remove the remove `zp_report_header` filter.
* Build - New hook on the form: `zp_form_after`.
* Build - Report header is now passed to the `zp_{$report_var}_report` filter.
* Build - Remove zp_get_php_timezone() because it is unnecessary.
* Build - Minified the form validation function, `zp_validate_form()`.
* Build - Removed back-compat.js legacy script.

= 1.8.5 =
* New - Use secure GeoNames api endpoint (https) for everyone, regardless of whether their site uses http or https, and regardless of whether they have a Premium GeoNames account or not. This renders the addon plugin "ZodiacPress Enhanced GeoNames" obsolete since this is now built in for everyone.

= 1.8.4 =
* Fix - Form was not allowing time offset of 0, for example, London timezone.
* Fix - Draw stellium planets closer together on the chart wheel. Conjunctions of 3 or more planets were being drawn too far apart from each other.

= 1.8.3 =
* Fix - Fixed a bug that was introduced in version 1.8.2. It had made the Natal Report Planets and Points settings, and Asepcts settings, appear blank.

= 1.8.2 =
* New - For creating own atlas database, get the cities data file from Google Cloud rather than from cosmicplugins.com.
* Tweak - check permissions for erase tools.

= 1.8.1 =
* Fix - Atlas database table INDEX needed max length.

= 1.8 =
* New - Option to create your own atlas database. Choosing this option will eliminate the need for GeoNames. However, the option to use GeoNames still remains.
* New - Eliminated the 'Next' button step. The form now only has a single Submit button. Previously, it required 2 steps: Next, and then Submit.
* New - Moved Sun above the Ascendant on the Birth Report.
* Fix - 'Planet in Next House' titles now honor the 'Hide Empty' setting.
* Tweak - Added Documentation link to top-right of admin pages, only on ZodiacPress admin pages.
* Tweak - Removed loading gif.
* Tweak - Added rel="noopener" to all target="_blank" links.

= 1.7.1 =
* Fix - The Fagan/Bradley sidereal method was not working when selling a report.

= 1.7 =
* New - New report to show only a chart drawing.
* Fix- Don't show section title if there are no interpretations for a whole section.
* Accessibility - The ZP admin pages, including settings and tools pages, will now have the correct page title in an H1 element. Previously, all tabs were inside the H1.
* Tweak - The form will now also update the offset when hour/minute is changed. Previously, the offset was updated only when the day/month/year/city was changed.
* Tweak - Scrollable results for autocomplete city field. The max number of cities returned from GeoNames is now 20. Previously, it was 12.
* Tweak - Add version query string to stylesheet link to ensure the latest stylesheet is shown even with cache.
* Maintenance - Updated the .pot language file.
 
= 1.6 =
* New - Add option for the birth report to skip a title when its interpretation is missing. This new "Hide Empty Titles" setting is at ZodiacPress > Settings > Natal Report tab > Display settings.
* Fix - On the form, remove prior hidden inputs in case of changing city. Previously, when changing city, many hidden inputs would accumulate.
* API - Add filter for form submit button text.
* API - Make wrapper for mktime(), zp_mktime().
* API - Make ZP_Birth_Report::get_interpretations() public.
* API - Make zp_tool_link() generic rather than only for cleanup tools.
* Tweak - Use admin-post.php to run Tools.

= 1.5.7 =
* Fix - Fixed some strings that were not being translated. The `.pot` translation file has been updated. Also added some notes for translators for the more comlplex strings.
* Accessibility - The report form now meets WCAG 2.0 guidelines at level AA. 
* Accessibility - Improved accessibility on the ZodicaPress Tools page in the admin by proper usage of heading elements.
* Tweak - Reorganized the settings sections in the Natal Report settings tab. This is in order to simplify the user experience. In that tab, two new sections have been introduced: Display, and Technical.
* Tweak - Reorganized the Tools > Cleanup tab.

= 1.5.6 =
* Fix - The chart image was not appearing on some browsers, mainly Safari (iPhone, iPad). This is now fixed by using a base64-encoded data uri instead of the image file.
* Tweak - move the Next/Submit button up just a bit for a better UX.
* Tweak - Load form template on backend also to prep for Gutenberg blocks.
* Tweak - Added the plugin version to the script url for the purpose of cache-busting.
* Tweak - Updated the birth year field to accept, the latest, 2019. 
* Optimize - Remove unused wp_ajax_nopriv_zp_customize_preview_image action since only logged-in users will be in the customizer.

= 1.5.5 =
* Tweak - Udpated the .pot language file.
* Tweak - Updated the error messages.
* API - Updated the plugin updater class.
* API - Remove safe_mode checks so as not to set off false positives for PHP7 compatibility checks.

= 1.5.4 =
* New - Improved error checking on the form. If there is an error while filling out the form, the user will get an error message with a description of the error. Technical: It now checks for GeoNames exceptions in the ajax response from the GeoNames webservice.
* New - Improvement for high-traffic sites: form can handle twice as many submissions per day. Each form submission now makes only 1 request to the GeoNames webservices. This means that it uses 1 GeoNames credit rather than 2 credits. The form can now handle a maximum of 30,000 requests per day, rather than 15,000.

= 1.5.3 =
* New - Check for missing swetest file. This improves the troubleshooting experience by notifying the user (only on ZodiacPress admin pages) if the file is missing. This is also added to the System Info on the Tools page.
* New - Now checks for GD image library support. This improves the troubleshooting experience by notifying the user (only on ZodiacPress admin pages) if GD support is missing.  This is also added to the System Info on the Tools page.
* New - Improved the form submission experience. It will now show a loading .gif while waiting for the "Next" button to become ready. The "Submit" button will now turn green to intuitively let the user know that the "Next" button has transformed into a "Submit" button.
* Fix - Fixed a bug that was causing the form to be disabled if a city with a modified timezone identifier was entered. This was affecting the form for many Asian cities. Technical: Fix uncaught exception for bad GeoNames timezone IDs. When this happens, it will now use the PHP timezone identifier instead of the GeoNames timezone id.
* API - Added a new hook, zp_report_shortcode_before, to allow swapping of the ZP JavaScript file by addons.
* Tweak - Load the plugin textdomain on init rather than on plugins_loaded. This may fix some translation issues.
* Tweak - Add sweph directory to $PATH correctly.

= 1.5.2 =
* Tweak - Escaped the chart drawing image src url.
* Tweak - Sanitized the chart drawing image element in the customizer with wp_kses_post.
* Tweak - Removed the site URL from System Info to make the System Info completely anonymous so that people who need support for this plugin will feel comfortable posting this info into the support forum. This allows for faster and more productive support.

= 1.5.1 = 
* Tweak - Improved form button styles for themes that do not already add cursor:pointer style to submission buttons. Also, the submit button will appear grayed out while it's not ready to be submitted.

= 1.5 =
* New - You can add a chart wheel to the birth report, either above or below the report. See https://isabelcastillo.com/docs/chart-wheel. The chart wheel colors can be changed in the WordPress Customizer, with the ability to preview the color changes on a sample chart wheel image right in the customizer.
* New - Added CSS styles for the form input:focus to highlight the input field that is being entered. This makes for a better user experience while filling out the form.
* New - Notify the user if JavaScript is disabled in their browser since the form will not work if Javascript is disabled.
* New - Updated the birth report form to accept a date with the year 2018.
* API - Removed the deprecated ZP_Chart::query_ephemeris method. Use the ZP_Ephemeris class instead.

= 1.4.1 =
* Tweak - Add disabled button CSS styles for themes that may not have any.

= 1.4 =
* New - Improved city field response. The Next/Submit button will be disabled until it is really ready. Previously, clicking Next too early would give a 'Please select a Birth City' error. This is because some things are happening in the background, for example, grabbing the city coordinates and timezone ID. If the background processes are not complete, you get an error. This problem should be greatly reduced now since the Next button will only be clickable when the background processes are complete.
* New - Add support for the Enhanced GeoNames extension which sends requests to GeoNames webservices from the browser rather than from the server side. This extension makes the city field and Next button faster and better.
* Fix - Only the caption should be bold in the report header data box, not the whole header data.
* Fix - Do not show Universal Time (GMT) on report header if birth time is unknown.
* Tweak - Better form styling and form fields alignment.
* API - Added $unknown_time property to ZP_Chart class to tell whether this report request was submitted with an unknown birth time.
* API - Added 3rd parameter to zp_report_header filter. The 3rd parameter is the chart object.
* API - ZP_Chart::cusps property is now public.
* API - The 2 functions, zp_extract_whole_degrees() and zp_extract_whole_minutes() have been merged into one function, zp_extract_degrees_parts(), that returns an array of the whole degrees and whole minutes.

= 1.3 =
* New - Option to use the Sidereal zodiac. Choose from 4 sidereal methods - Hindu/Lahiri, Fagan/Bradley, Raman, or Krishnamurti.
* New - You can now set the house system to be used in the shortcode. 12 house systems are included.
* New - The report header will now show which zodiac is used, whether Tropical or Sidereal.
* New - The report header will now show Universal Time in addition to the local time formats.
* New - If birth time is unknown, check for ingress on that day. Let the person know that the planet changed signs on that day, and from which sign to which it changed.
* New - Add filter to omit name field on form.
* New - Allow Start Over link to be removed with a filter.
* New - Added a Feedback link in the ZP admin.
* New - New ZP_Ephemeris class to query the Swiss Ephemeris to separate this from the ZP_Chart class. The ZP_Chart::query_ephemeris method is deprecated. Use the new ZP_Ephemeris instead
* Fix - the Birth City field was broken and/or missing many cities because urlencode() was breaking the autocomplete cities list.
* Tweak - Update Lilith's label to Black Moon Lilith.
* Tweak - Simplified form no longer shows coordinates.
* Tweak - Force the PHP mktime() function to use UTC when creating the unix timestamp for the chart since mktime() uses whatever zone its server wants. This is to prevent giving bad times in case some server is not using UTC.

= 1.2 =
* New - Added granular control over orbs. Custom orbs can now be set per each type of aspect and per each planet.
* Fix - Birth report was not working on https/SSL/encrypted pages. The free Geonames webservices only serves over http. The call to Geonames is now made from the server side, rather than in the browser, to support https/SSL.
* Fix - Orb setting was stuck on 8 even if a custom orb was set.
* Maintenance - Updated the .pot language file.

= 1.0 =
* Initial public release.

== Upgrade Notice ==

= 2.0.1 = 
If upgrading from 2.0, this is a safe minor release. If upgrading from 1.x, read 2.0 changelog BEFORE upgrading.

= 2.0 = 
NOTE - Read the changelog BEFORE updating because 2.0 brings important changes.

= 1.8.5 =
New - Use secure GeoNames api endpoint (https) for everyone.

= 1.8.4 =
Fixed - Form was not allowing time offset of 0, for example, London timezone.

= 1.8.3 =
Fix - Fixed a bug that made the Natal Report Planets and Points settings, and Asepcts settings, appear blank.

= 1.8.1 =
Fixed - New Atlas database table INDEX needed max length.

= 1.8 =
New option to create your own atlas database and be rid of GeoNames.

= 1.7.1 =
Fixed - The Fagan/Bradley sidereal method was not working when selling a report.

= 1.6 =
New option for report to skip a title when its interpretation is missing.

= 1.5.7 =
Fixed some strings that were not being translated, updated .pot translation file, and improved accessibility.

= 1.5.6 =
Fixed the chart image. It was not appearing on some browsers, mainly Safari (iPhone, iPad).

= 1.5.4 =
Improved error checking on form. Improvement for high-traffic sites: form can handle twice as many submissions per day.

= 1.5.3 =
Fixed a bug that was causing the form to be disabled if a city with a modified timezone identifier was entered.

= 1.5.1 =
Improved form button styles.

= 1.5 =
New - You can add a chart wheel to the birth report.

= 1.4.1 =
Improved city field response on the form.

= 1.4 =
Improved city field response on the form.

= 1.3 =
Fixes the Birth City field.

= 1.2 =
NEW - Orb controls. FIX - Now works on https/SSL encrypted pages.

= 1.0 =
* Initial public release.
