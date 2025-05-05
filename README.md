# easyTRIAS – Proxy API for the TRIAS interface
**easyTRIAS** is an unofficial PHP-based proxy API for querying **real-time departure data from stops** via the **TRIAS interface**.

The API supports **multiple output formats**, **simple caching**, and can be deployed on any standard web server with minimal setup.

## ℹ️ What is TRIAS?

TRIAS is an **XML-based, standardized exchange format for travel information systems** developed by the VDV (Verband Deutscher Verkehrsunternehmen). It is used for structured communication between passenger information systems, timetable services, and third-party applications.

TRIAS is flexible, standardized, and powerful – but is **currently not in widespread use in Germany**. However, many transport companies and timetable data pools are working on deploying TRIAS or are in pilot phases.

You can find more information about TRIAS and its documentation here: https://www.vdv.de/ip-kom-oev.aspx

## 🔧 Requirements
- Web server with **PHP 7.2 or higher**

- **Write permissions** for the configured cache directory

- **Access to a TRIAS endpoint** (e.g., via [Connect Niedersachsen](https://connect-fahrplanauskunft.de/datenbereitstellung) or [MobiData BW](https://www.mobidata-bw.de/dataset/trias))

## 📦 Features
- Query real-time departure data via the TRIAS interface

- Multiple output formats: `json`, `xml`, `csv`, `html`, `raw`

- Caching to reduce external API requests

- Walking time filtering

- Transport Types with icon & color coding

## 🧮 Input parameters
| Parameter | Type | Required | Default | Description |
|------------------|----------|----------|-----------|---------------|
| `stopPointRef` | string | ✅ | – | TRIAS stop ID (e.g. `de:03241:31`) |
| `format` | string | ❌ | `html` | Output format (`json`, `xml`, `csv`, `html`, `raw`) |
| `walkingMinutes` | integer | ❌ | `0` | Walking time to the stop in minutes – earlier departures are filtered |
| `forceRefresh` | `0`/`1` | ❌ | `0` | If set to `1`, the cache will be bypassed and a fresh request is made |

## 🧪 Example calls
1. **HTML table:**
   ```bash
   https://example.com/departures.php?stopPointRef=de:03241:31
   ```
2. **JSON with walking time filter:**
   ```bash
   https://example.com/departures.php?stopPointRef=de:03241:31&format=json&walkingMinutes=5
   ```
3. **Raw TRIAS XML for debugging:**
   ```bash
   https://example.com/departures.php?stopPointRef=de:03241:31&format=raw
   ```

## 🗃️ Caching
- Each query is cached for **10 minutes** by default to reduce external API calls

- Cache files are stored in the `/cache` directory, which is created automatically if it doesn't exist

- You can change the cache duration and directory path in the `/.env.ini` configuration file

- To disable caching, set the cache duration to `0`.

## 🎨 Transport icons & colors
- Each transport mode (e.g., bus, train, tram) can be assigned a unique icon and color

- These mappings are defined in the `/src/Dictionaries.php` file

- The base URL for icon images can be configured in the `/.env.ini` file

## ⚖️ License
This project is licensed under the [Apache 2.0 License](https://opensource.org/license/apache-2-0).

You may **freely use, modify, and redistribute** the code, even in commercial projects – as long as the license and copyright notices are retained. Liability is excluded.

## 🤝 Author
👤 Dennis Krüger

📍 Hanover, Germany

🌐 [https://www.denniskr.de](https://www.denniskr.de)

## 📬 Contact / Feedback
Do you have ideas, feedback, or would like to collaborate?

Feel free to open an issue on GitHub or reach out directly via email: easytrias@denniskr.de