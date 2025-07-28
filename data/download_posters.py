import os
import json
import requests
import mimetypes

DATA_FILE = "anime-moods.json"
OUTPUT_DIR = "posters"
REPORT_FILE = "posters-report.txt"
BASE_ASSETS_PATH = "/assets/posters/"
DEFAULT_POSTER_FILENAME = "default.jpg"
DEFAULT_POSTER_PATH = os.path.join(OUTPUT_DIR, DEFAULT_POSTER_FILENAME)

# Ensure folders and default image
os.makedirs(OUTPUT_DIR, exist_ok=True)
if not os.path.exists(DEFAULT_POSTER_PATH):
    raise FileNotFoundError("Missing fallback image: posters/default.jpg")

# Load JSON
with open(DATA_FILE, "r", encoding="utf-8") as f:
    data = json.load(f)

# Report lines
report_lines = []

downloaded = 0
skipped = 0
updated = 0
fallbacks = 0

def safe_filename(title, ext):
    return title.lower().replace(" ", "-").replace(":", "").replace("'", "").replace("!", "").replace("?", "") + ext

def get_extension(content_type):
    ext = mimetypes.guess_extension(content_type.split(";")[0])
    return ext if ext else ".jpg"

for mood, entries in data.items():
    for anime in entries:
        title = anime.get("title")
        if not title:
            continue

        try:
            print(f"🔍 Searching: {title}")
            search = requests.get("https://api.jikan.moe/v4/anime", params={"q": title, "limit": 1})
            search.raise_for_status()
            result = search.json()

            image_url = result["data"][0]["images"]["jpg"]["large_image_url"]
            img_resp = requests.get(image_url, timeout=10)
            img_resp.raise_for_status()

            content_type = img_resp.headers.get("Content-Type", "")
            ext = get_extension(content_type)
            filename = safe_filename(title, ext)
            local_path = os.path.join(OUTPUT_DIR, filename)

            if not os.path.exists(local_path):
                with open(local_path, "wb") as out_file:
                    out_file.write(img_resp.content)
                print(f"⬇️ Downloaded: {filename}")
                report_lines.append(f"✔️  Downloaded: {title} -> {filename}")
                downloaded += 1
            else:
                print(f"✅ Already exists: {filename}")
                report_lines.append(f"✅ Skipped: {title} (exists)")
                skipped += 1

            anime["poster"] = BASE_ASSETS_PATH + filename
            updated += 1

        except Exception as e:
            print(f"⚠️ Error fetching {title}: {e}")

            # Use existing file if it already exists
            fallback_name = safe_filename(title, ".jpg")
            fallback_path = os.path.join(OUTPUT_DIR, fallback_name)

            if os.path.exists(fallback_path):
                print(f"🔁 Using previously downloaded: {fallback_name}")
                anime["poster"] = BASE_ASSETS_PATH + fallback_name
                report_lines.append(f"🔁 Fallback to existing: {title} -> {fallback_name}")
                skipped += 1
            else:
                anime["poster"] = BASE_ASSETS_PATH + DEFAULT_POSTER_FILENAME
                print(f"🛑 Using default image for: {title}")
                report_lines.append(f"🛑 Used default: {title}")
                fallbacks += 1

# Save updated JSON
with open(DATA_FILE, "w", encoding="utf-8") as f:
    json.dump(data, f, indent=2, ensure_ascii=False)

# Write report file
with open(REPORT_FILE, "w", encoding="utf-8") as rf:
    rf.write("PickMyAnime – Poster Import Report\n")
    rf.write("="*40 + "\n\n")
    rf.write(f"Downloaded: {downloaded}\nSkipped (exists): {skipped}\nUsed Fallback: {fallbacks}\n\n")
    rf.write("\n".join(report_lines))

print(f"\n📄 Report saved to: {REPORT_FILE}")
print(f"✅ Done. {downloaded} downloaded, {skipped} skipped, {fallbacks} fallback used.")
