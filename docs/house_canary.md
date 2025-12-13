GET
Census Information
https://api.housecanary.com/v2/property/census?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Identify US Census geographic areas for a given property, useful for demographic analysis and regional comparisons.

Source: Census TIGER Data

Pricing Tier: Included with Subscription

Updated: Annually

GET
Rental Comparables
https://api.housecanary.com/v3/property/comps_rental?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&min_sim_score=93&min_date=2020-07-09&status=ACTIVE&additional_data=property/value&num_comps=2&distance=10
Obtain quantitatively-derived rental comparables for properties using HouseCanary's proprietary similarity score algorithm, assisting in rental market analysis.

Filter rental comps based on your selection criteria and pull down additional property data elements for the returned comps in a single API request.

Source: HouseCanary

Pricing Tier: Premium Plus

Updated: 15 minutes

GET
Sales Comparables
https://api.housecanary.com/v3/property/comps_sale?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&min_sim_score=93&min_date=2020-07-09&status=ACTIVE&additional_data=property/value&num_comps=2&distance=10
Access quantitatively-derived sales comparables for properties using HouseCanary's proprietary similarity score algorithm, crucial for accurate market comparisons.

Filter comps based on your selection criteria and pull down additional property data elements for the returned comps in a single API request.

Source: HouseCanary

Pricing Tier: Premium Plus

Updated: 15 minutes

GET
Property Details Advanced
https://api.housecanary.com/v3/property/details_advanced?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&type=both
Access various property attributes from county assessor records, enabling comprehensive property profiling, along with HouseCanary data with additional information

Source: HouseCanary, Public Records

Pricing Tier: Basic

Updated: Daily

GET
Estimated property value
https://api.housecanary.com/v3/property/estimate?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Our Property Value Estimation API gives a rough idea of a property's current worth within a set range of accuracy. It uses a reliable Automated Valuation Model and a special rounding method to ensure fairness and security. This method balances speed and accuracy, providing useful estimates for different property types while aiming to be as unbiased as possible

Source: HouseCanary

Pricing Tier: Basic

Updated: 15 minutes

GET
Disaster Area Details
https://api.housecanary.com/v2/property/fema_disaster_area?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&order=asc&limit=10&start=2019-01-01&end=2019-01-01
Acquire details on open FEMA disaster declarations for a property's county, informing risk assessments and insurance needs.

Source: FEMA

Pricing Tier: Basic

Updated: Daily

GET
Flood Risk Information
https://api.housecanary.com/v2/property/flood?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Retrieve FEMA flood risk data for properties, essential for evaluating potential hazards and insurance requirements.

Source: FEMA

Pricing Tier: Basic

GET
HOA Fees
https://api.housecanary.com/v3/property/hoa_est?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Estimate annual HOA fees for properties using HouseCanary's proprietary algorithms, important for budgeting and investment calculations.

Source: HouseCanary

Pricing Tier: Premium

Updated: Annually

https://api.housecanary.com/v3/property/historical_value?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&date="2021-06-01" or "2022-03-01T12:34:56Z"
HouseCanary's flagship historical automated property value estimate.

HouseCanary's proprietary Automated Valuation Model (AVM) utilizes a unique machine learning algorithm on top of multiple input data sources to estimate the current market value of a residential property. Responses include upper and lower bounds, as well as a forecast standard deviation (FSD) for transparency into model confidence.

Pricing Tier: Premium

GET
Land Value
https://api.housecanary.com/v2/property/land_value?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
HouseCanary's automated land value estimate, assisting in land pricing and investment analysis.

HouseCanary's proprietary Automated Land Valuation Model utilizes a unique machine learning algorithm on top of multiple input data sources to estimate the current market value of the land contained on the parcel. Responses include upper and lower bounds, as well as a forecast standard deviation (FSD) for transparency into model confidence.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly


GET
Estimated LTV Details
https://api.housecanary.com/v2/property/ltv_details?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Access comprehensive LTV data for properties, supporting mortgage and refinance decision-making. Access comprehensive data on the estimated loan-to-value ratio (LTV) for properties. Receive granular information on grantors, grantees, monthly payments, current interest rates, original lien amounts, loan duration, progress made, outstanding principal, principal paid, record and due dates, ARM specifics, and more. Additionally, the endpoint delivers summary data covering equity, monthly payments, lien amounts, notice IDs, and property valuations.

Active liens' paydown is calculated based on available lien terms, factoring in details such as ARM and interest-only periods when applicable. The response includes a sorted list of notice IDs with corresponding descriptions, which highlight findings during LTV generation and their usual impact on the calculation. This is particularly useful for helping drive refinancing or loan origination analysis.

Source: HouseCanary, Public Records, Freddie Mac

Pricing Tier: Premium

Updated: Monthly

GET
Mortgage Liens - All
https://api.housecanary.com/v2/property/mortgage_lien_all?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&order=asc&limit=10&start=2015-01-01&end=2015-01-01
View all available liens/mortgages, including those from previous owners, for comprehensive property lien history.

Source: HouseCanary, Public Records

Pricing Tier: Basic

Updated: Daily

GET
Notice of Default
https://api.housecanary.com/v2/property/nod?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&order=asc&limit=10&start=2015-01-01&end=2015-01-01
Obtain non-rescinded Notice of Default (NOD) events for properties, useful for assessing potential foreclosure risks and investment viability.

Source: HouseCanary, Public Records

Pricing Tier: Basic

Updated: Daily

GET
Owner Occupied
https://api.housecanary.com/v2/property/owner_occupied?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Determine owner-occupancy status, informing potential rental opportunities and neighborhood compositions.

Source: HouseCanary, Public Records

Pricing Tier: Basic

Updated: Annually

GET
Rental Value Forecast
https://api.housecanary.com/v2/property/rental_value_forecast?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
HouseCanary's 1-year rental value forecasts, supporting rental market outlooks and investment planning.

Fields include forecast % growth (decline) at 3, 6, and 12 month intervals into the future.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly

GET
Rental value by lat/long
https://api.housecanary.com/v2/property/rental_value_lat_long?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&lat=3185.4225224875286&lon=3185.4225224875286&bed=8511&bath=3185.4225224875286&sqft=8511&lot_sqft=8511&property_type=TH&garage=3185.4225224875286&year_built=8511&min_comp_similarity_score=4
Retrieve rental value estimates based on specific property parameters and geographic coordinates (latitude and longitude). Receive insights into the potential rental income for a property located at a given latitude and longitude, considering factors like bedrooms, bathrooms, square footage, lot size, property type, garage spaces, and year built.

Source: HouseCanary

Pricing Tier: Refer to Pricing Page

Updated: 15 minutes

GET
Rental Value Distribution
https://api.housecanary.com/v2/property/rental_value_within_block?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&client_value=3500&client_value_sqft=3
Compare a property's rental value and value per sq ft within its block, useful for local market positioning.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly


GET
Rental Value
https://api.housecanary.com/v2/property/rental_value?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
HouseCanary's flagship automated rental value estimate, essential for rental pricing strategies and market analysis.

HouseCanary's proprietary Automated Rental Valuation Model utilizes a unique machine learning algorithm on top of multiple input data sources to estimate the current market rent of a residential property. Responses include upper and lower bounds, as well as a forecast standard deviation (FSD) for transparency into model confidence.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly

GET
Sales History
https://api.housecanary.com/v2/property/sales_history?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&order=asc&limit=10&start=2015-01-01&end=2015-01-01
Examine a property's sales and ownership transfer history for trends, transaction patterns, and investment insights.

Source: Public Records

Pricing Tier: Basic

Updated: Daily


GET
School Information
https://api.housecanary.com/v2/property/school?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Obtain details on nearby schools for properties, valuable for assessing neighborhood quality.

Source: HouseCanary calculation, State standard school testing

Pricing Tier: Basic


GET
Tax History
https://api.housecanary.com/v2/property/tax_history?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&order=asc&limit=10&start=2015-01-01&end=2015-01-01
Review current and historical tax and assessment values, aiding in property valuation and tax planning.

Source: Public Records

Pricing Tier: Basic

Updated: Annually


GET
Value Analysis
https://api.housecanary.com/v2/property/value_analysis?slug=1624-E-Dogwood-Ln-Gilbert-AZ-85295&street_address=123 Main St San Francisco CA 94132&zipcode=94132&estimated_value=325000&gla_sqft=2750&include_comp_based_analysis=true
Value Analysis provides a set of automated checks to test the feasibility of a property value based on HouseCanary’s data and models. It provides a recommended approach for coming to a high-confidence value for the property.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly


GET
Value with Adjustments
https://api.housecanary.com/v2/property/value_details_adjusted?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&add_beds=2&add_baths=1&add_sqft=400&add_pools=1
Estimate adjusted property values based on potential modifications, useful for evaluating renovation opportunities and ROI. At least one of the following parameters is required to adjust value: addition of bedrooms, bathrooms, square footage, or pool.

If multiple details are specified in one request, the adjusted_value_to in the response will reflect the combined effect of all adjustments - it will not return separate values for each individual adjustment. To determine the value effects of several different adjustments, multiple queries are required, but can be combined into one POST request.

If we cannot perform the adjustment for any reason, a 204 response will be returned and the call will not be charged.

Source: HouseCanary

Pricing Tier: Basic

Updated: Monthly


GET
Value Forecast
https://api.housecanary.com/v2/property/value_forecast?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
HouseCanary's 3-year home price forecasts, essential for understanding market trends and making informed investment decisions.

Home values are forecast 3 years into the future using HouseCanary's AVM values and home price index (HPI) at a zip code level. Fields include forecast % growth (decline) at the following monthly intervals into the future: 3, 6, 12, 18, 24, 30 and 36.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly

GET
Value with FSD threshold filter
https://api.housecanary.com/v3/property/value_fsd_threshold?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&fsd_threshold=0.05
HouseCanary's proprietary Automated Valuation Model (AVM) utilizes a unique machine learning algorithm on top of multiple input data sources to estimate the current market value of a residential property. Responses include upper and lower bounds, as well as a forecast standard deviation (FSD) for transparency into model confidence. If the optional FSD threshold is supplied, customers are not billed for responses containing AVMs not meeting the threshold.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly

GET
Crime Information From Address
https://api.housecanary.com/v2/property/block_crime?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Access crime data from the past two years near a specific block, including percentile rankings for county and nationwide comparison, enabling better understanding of local neighborhood statistics.

Pricing Tier: Basic

Updated: Monthly

GET
Value Historical From Address
https://api.housecanary.com/v2/property/blockgroup_value_ts_historical?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&property_type=SFD&order=asc&limit=10&start=2015-01-01&end=2015-01-01
Retrieve historical time series data for monthly blockgroup-median property values and dollar values per sq ft, offering valuable insights into past market trends and performance. Historical data available back to 1985 or after depending on local market data availability.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly

GET
Affordability Forecast From Address
https://api.housecanary.com/v2/property/zip_affordability_ts_forecast?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105&order=asc&limit=10&start=2015-01-01&end=2015-01-01
Access forecasts of monthly affordability values for ZIP codes with coverage three years into the future, enabling informed decisions about property investments and market trends. Higher values imply lower affordability.

Source: HouseCanary

Pricing Tier: Basic

Updated: Monthly

GET
Gross Rental Yield From Address
https://api.housecanary.com/v2/property/zip_hcri?slug=123-Main-St-Apt-1-San-Francisco&address=123 Main St&unit=Apt 1&city=San Francisco&state=CA&zipcode=94105
Calculate the Canary Rental Index (CRI) to measure aggregated gross rental yield across a ZIP code, enabling assessment of rental market performance within a specific ZIP code.

Gross rental yield is calculated per-property as the ( monthly rental AVM * 12 ) / property value AVM.

Source: HouseCanary

Pricing Tier: Premium

Updated: Monthly


GET
Market Pulse MSA Latest
https://api.housecanary.com/v3/msa/market_pulse/latest?msa=38060
Access the latest insights into rental supply and demand changes seen through active single-family detached inventory and pricing levels across MSAs. Receive in-depth market trend analysis for informed decision-making in a single API call.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly
GET
Market Pulse MSA Historical
https://api.housecanary.com/v3/msa/market_pulse/timeseries?msa=38060&start_date=2024-11-01&end_date=2024-12-01
Access the latest insights into supply and demand changes seen through active single-family detached inventory and pricing levels across MSAs. Receive in-depth market trend analysis for informed decision-making in a single API call.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly
GET
Market Pulse MSA Rental Latest
https://api.housecanary.com/v3/msa/market_pulse_rental/latest?msa=38060
Access the latest insights into rental supply and demand changes seen through active single-family detached inventory and pricing levels across MSAs. Receive in-depth market trend analysis for informed decision-making in a single API call.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly

GET
Market Pulse MSA Rental Historical
https://api.housecanary.com/v3/msa/market_pulse_rental/timeseries?msa=38060&start_date=2024-11-01&end_date=2024-12-01
Explore rental supply and demand changes for single-family detached properties across MSAs over a requested time range, enabling in-depth rental market trend analysis and historical performance evaluation.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly
GET
Market Pulse State Latest
https://api.housecanary.com/v3/state/market_pulse/latest?state=CA
Access the latest insights into supply and demand changes seen through active single-family detached inventory and pricing levels within most states across the US. Receive in-depth market trend analysis for informed decision-making in a single API call.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly

GET
Market Pulse State Rental Latest
https://api.housecanary.com/v3/state/market_pulse_rental/latest?state=CA
Explore rental supply and demand changes for single-family detached properties within most states across the US over a requested time range, enabling in-depth rental market trend analysis and historical performance evaluation.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly
GET
Market Pulse State Rental Historical
https://api.housecanary.com/v3/state/market_pulse_rental/timeseries?state=CA&start_date=2024-11-01&end_date=2024-12-01
Explore rental supply and demand changes for single-family detached properties within most states across the US over a requested time range, enabling in-depth rental market trend analysis and historical performance evaluation.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly

GET
Market Pulse State Historical
https://api.housecanary.com/v3/state/market_pulse/timeseries?state=CA&start_date=2024-11-01&end_date=2024-12-01
Explore supply and demand changes for single-family detached properties within most states across the US over a requested time range, enabling in-depth market trend analysis and historical performance evaluation.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekl

GET
Market Pulse Zipcode Latest
https://api.housecanary.com/v3/zip/market_pulse/latest?zipcode=33019
Access the latest insights into supply and demand changes seen through active single-family detached inventory and pricing levels within a broad range of ZIP codes across the US. Receive in-depth market trend analysis for informed decision-making in a single API call.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly


GET
Market Pulse Zipcode Historical
https://api.housecanary.com/v3/zip/market_pulse/timeseries?zipcode=33019&start_date=2024-11-01&end_date=2024-12-01
Explore supply and demand changes for single-family detached properties across US zip codes over a requested time range, enabling in-depth market trend analysis and historical performance evaluation.

Source: HouseCanary

Pricing Tier: Included with Subscription

Updated: Weekly

