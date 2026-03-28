const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..");
const EXPORT_DIR = path.join(ROOT, "Wordpress export");
const XML_PATH = path.join(EXPORT_DIR, "wordpress.2014-07-02.xml");
const BLOG_DIR = path.join(EXPORT_DIR, "blog");
const OUTPUT_DIR = path.join(ROOT, "site");
const POSTS_PER_PAGE = 12;
const BUILD_VERSION = String(Date.now());

const XML = fs.readFileSync(XML_PATH, "utf8");

function ensureDir(dirPath) {
  fs.mkdirSync(dirPath, { recursive: true });
}

function cleanOutput() {
  fs.rmSync(OUTPUT_DIR, { recursive: true, force: true });
  ensureDir(OUTPUT_DIR);
}

function writeFile(relativePath, content) {
  const filePath = path.join(OUTPUT_DIR, relativePath);
  ensureDir(path.dirname(filePath));
  fs.writeFileSync(filePath, content);
}

function copyIfExists(source, destination) {
  if (!fs.existsSync(source)) {
    return;
  }

  fs.cpSync(source, destination, { recursive: true });
}

function stripCdata(value) {
  if (!value) {
    return "";
  }

  return value
    .replace(/^<!\[CDATA\[/, "")
    .replace(/\]\]>$/, "");
}

function decodeEntities(value) {
  if (!value) {
    return "";
  }

  const named = {
    amp: "&",
    lt: "<",
    gt: ">",
    quot: '"',
    apos: "'",
    nbsp: " "
  };

  return value.replace(/&(#x?[0-9a-fA-F]+|[a-zA-Z]+);/g, (match, entity) => {
    if (entity[0] === "#") {
      const hex = entity[1].toLowerCase() === "x";
      const codePoint = Number.parseInt(entity.slice(hex ? 2 : 1), hex ? 16 : 10);
      return Number.isFinite(codePoint) ? String.fromCodePoint(codePoint) : match;
    }

    return Object.prototype.hasOwnProperty.call(named, entity) ? named[entity] : match;
  });
}

function normalizeSlug(value) {
  const input = String(value ?? "").trim();
  if (!input) {
    return "";
  }

  try {
    return decodeURIComponent(input);
  } catch {
    return input;
  }
}

function escapeHtml(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function stripTags(value) {
  return decodeEntities(String(value ?? "").replace(/<[^>]*>/g, " "));
}

function normalizeWhitespace(value) {
  return value.replace(/\s+/g, " ").trim();
}

function getSingleTag(block, tagName) {
  const escaped = tagName.replace(/[-/\\^$*+?.()|[\]{}]/g, "\\$&");
  const match = block.match(new RegExp(`<${escaped}[^>]*>([\\s\\S]*?)</${escaped}>`, "i"));
  return match ? stripCdata(match[1]).trim() : "";
}

function getAllTagContents(block, tagName) {
  const escaped = tagName.replace(/[-/\\^$*+?.()|[\]{}]/g, "\\$&");
  const matches = block.matchAll(new RegExp(`<${escaped}[^>]*>([\\s\\S]*?)</${escaped}>`, "gi"));
  return Array.from(matches, (match) => stripCdata(match[1]).trim());
}

function getCategoryEntries(block) {
  const matches = block.matchAll(/<category\b([^>]*)>([\s\S]*?)<\/category>/gi);
  return Array.from(matches, (match) => {
    const attrs = match[1];
    const domainMatch = attrs.match(/domain="([^"]+)"/i);
    const nicenameMatch = attrs.match(/nicename="([^"]+)"/i);

    return {
      domain: domainMatch ? decodeEntities(domainMatch[1]) : "",
      nicename: nicenameMatch ? normalizeSlug(decodeEntities(nicenameMatch[1])) : "",
      label: decodeEntities(stripCdata(match[2]).trim())
    };
  });
}

function parseWpCategories(xml) {
  return Array.from(xml.matchAll(/<wp:category>([\s\S]*?)<\/wp:category>/g), (match) => {
    const block = match[1];
    return {
      id: getSingleTag(block, "wp:term_id"),
      slug: normalizeSlug(getSingleTag(block, "wp:category_nicename")),
      name: decodeEntities(getSingleTag(block, "wp:cat_name"))
    };
  });
}

function parseComments(block) {
  return Array.from(block.matchAll(/<wp:comment>([\s\S]*?)<\/wp:comment>/g), (match) => {
    const commentBlock = match[1];
    const approved = getSingleTag(commentBlock, "wp:comment_approved");

    if (approved !== "1") {
      return null;
    }

    return {
      author: decodeEntities(getSingleTag(commentBlock, "wp:comment_author")) || "Anonymous",
      date: getSingleTag(commentBlock, "wp:comment_date"),
      content: getSingleTag(commentBlock, "wp:comment_content")
    };
  }).filter(Boolean);
}

function buildExcerpt(html) {
  const text = normalizeWhitespace(stripTags(html));
  if (text.length <= 220) {
    return text;
  }

  return `${text.slice(0, 217).trimEnd()}...`;
}

function formatDate(dateString) {
  const date = new Date(`${dateString.replace(" ", "T")}Z`);
  return new Intl.DateTimeFormat("he-IL", {
    year: "numeric",
    month: "long",
    day: "numeric"
  }).format(date);
}

function rewriteContent(html, context) {
  if (!html) {
    return "";
  }

  let output = html;

  output = output.replace(/https?:\/\/(https?:\/\/)/gi, "$1");
  output = output.replace(/https?:\/\/www\.amirdotan\.com\/blog\/wp-content\/uploads\//gi, "/wp-content/uploads/");
  output = output.replace(/https?:\/\/www\.amirdotan\.com\/blog\/blog_images\//gi, "/blog_images/");
  output = output.replace(/https?:\/\/www\.amirdotan\.com\/blog\/wp-content\/themes\/justsimple-10\/img\/tile\.gif/gi, "/assets/tile.gif");

  output = output.replace(
    /https?:\/\/www\.amirdotan\.com\/blog\/\?p=(\d+)/gi,
    (_, id) => context.postUrlsById.get(id) || `/legacy/?p=${id}`
  );

  output = output.replace(
    /https?:\/\/www\.amirdotan\.com\/blog\/\?attachment_id=(\d+)/gi,
    (_, id) => context.attachmentUrlsById.get(id) || `/legacy/?attachment_id=${id}`
  );

  output = output.replace(
    /https?:\/\/www\.amirdotan\.com\/blog\/\?cat=(\d+)/gi,
    (_, id) => context.categoryUrlsById.get(id) || `/legacy/?cat=${id}`
  );

  output = output.replace(/https?:\/\/www\.amirdotan\.com\/blog(\/[^\s"'<>]+)/gi, "$1");
  output = output.replace(/https?:\/\/www\.amirdotan\.com\/blog\//gi, "/");
  output = output.replace(/https?:\/\/\/(?=(posts|page|categories|archive|wp-content|blog_images|legacy)\/)/gi, "/");

  return output;
}

function formatContentHtml(html) {
  const normalized = String(html ?? "").replace(/\r\n/g, "\n").trim();
  if (!normalized) {
    return "";
  }

  const hasBlockMarkup = /<(p|div|section|article|aside|header|footer|main|nav|h[1-6]|ul|ol|li|blockquote|pre|table|figure|hr|iframe)\b/i.test(
    normalized
  );

  if (hasBlockMarkup) {
    return normalized;
  }

  return normalized
    .split(/\n\s*\n+/)
    .map((paragraph) => paragraph.replace(/\n+/g, " ").trim())
    .filter(Boolean)
    .map((paragraph) => `<p>${paragraph}</p>`)
    .join("\n");
}

function parseItems(xml, taxonomyMap) {
  return Array.from(xml.matchAll(/<item>([\s\S]*?)<\/item>/g), (match) => {
    const block = match[1];
    const postType = getSingleTag(block, "wp:post_type");
    const status = getSingleTag(block, "wp:status");
    const id = getSingleTag(block, "wp:post_id");
    const slug = normalizeSlug(getSingleTag(block, "wp:post_name")) || id;
    const title = decodeEntities(getSingleTag(block, "title")) || slug;
    const date = getSingleTag(block, "wp:post_date");
    const content = getSingleTag(block, "content:encoded");
    const excerpt = getSingleTag(block, "excerpt:encoded");
    const categories = getCategoryEntries(block)
      .filter((entry) => entry.domain === "category")
      .map((entry) => ({
        slug: entry.nicename,
        name: entry.label || taxonomyMap.get(entry.nicename) || entry.nicename
      }));

    return {
      id,
      postType,
      status,
      slug,
      title,
      link: decodeEntities(getSingleTag(block, "link")),
      date,
      content,
      excerpt,
      categories,
      comments: parseComments(block),
      attachmentUrl: decodeEntities(getSingleTag(block, "wp:attachment_url"))
    };
  });
}

function postUrl(post) {
  const [year, month] = post.date.split(" ")[0].split("-");
  return `/posts/${year}/${month}/${post.slug}/`;
}

function attachmentUrl(item) {
  const attachmentPath = item.attachmentUrl.replace(/^https?:\/\/www\.amirdotan\.com\/blog\//i, "/");
  return attachmentPath || `/media/${item.slug}/`;
}

function paginate(items, perPage) {
  const pages = [];
  for (let index = 0; index < items.length; index += perPage) {
    pages.push(items.slice(index, index + perPage));
  }
  return pages;
}

function pageTitle(title) {
  return `${title} | עיצוב שמיש`;
}

function renderSidebar(sidebarData) {
  const categoryItems = sidebarData.categories
    .slice(0, 18)
    .map(
      (category) =>
        `<li><a href="/categories/${category.slug}/">${escapeHtml(category.name)}</a> <span>${category.posts.length}</span></li>`
    )
    .join("");

  const monthItems = sidebarData.monthArchive
    .map(
      (month) =>
        `<li><a href="/archive/${month.year}/${month.value}/">${escapeHtml(month.label)} ${month.year}</a> <span>${month.posts.length}</span></li>`
    )
    .join("");

  return `<aside class="sidebar" aria-label="ניווט משני">
  <form class="sidebar-search" action="/" method="get">
    <label class="sidebar-search__label" for="sidebar-search-input">חפש</label>
    <input id="sidebar-search-input" class="sidebar-search__input" type="text" name="q">
  </form>
  <section class="sidebar-card sidebar-card--profile">
    <img class="sidebar-profile__image" src="/blog_images/me2.jpg" alt="אמיר דותן">
    <h2>אמיר דותן</h2>
    <p>חי בלונדון מאז שנת 2001. מלמד כבר ארבע שנים עיצוב ממשקים, שמישות וקורסים העוסקים בחלק מלימודי התואר הראשון במכללת SAE. מלמד לתואר שני באינטראקציית אדם-מחשב.</p>
    <p class="sidebar-profile__email">amirdotan1 [at] gmail.com</p>
  </section>
  <section class="sidebar-card">
    <h2>קטגוריות</h2>
    <ul class="sidebar-list">
      ${categoryItems}
    </ul>
  </section>
  <section class="sidebar-card">
    <h2>ארכיון חודשי</h2>
    <ul class="sidebar-list">
      ${monthItems}
    </ul>
  </section>
</aside>`;
}

function layout({ title, description = "", content, canonical = "/", sidebarData = null }) {
  const safeDescription = escapeHtml(description);
  const safeTitle = escapeHtml(title);
  const sidebar = sidebarData ? renderSidebar(sidebarData) : "";
  const archiveNotice = sidebarData
    ? `<div class="archive-notice">זהו ארכיון סטטי של הבלוג שכתבתי בין השנים 2005 ו-2013, ובו ${sidebarData.totalPosts} פוסטים שפורסמו לאורך השנים. הבלוג שוחזר במרץ 2026. חלק מהתמונות המקוריות בפוסטים חסרות.</div>`
    : "";

  return `<!doctype html>
<html lang="he" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>${safeTitle}</title>
  <meta name="description" content="${safeDescription}">
  <link rel="canonical" href="${canonical}">
  <link rel="stylesheet" href="/styles/site.css?v=${BUILD_VERSION}">
</head>
<body>
  <main class="page-shell">
    <div class="outer-shell">
      <header class="site-header">
        <div class="site-header__inner">
          <h1 class="site-title"><a href="/">עיצוב שמיש</a></h1>
          <p class="site-description">בלוג בנושא שמישות וחוויית משתמש</p>
        </div>
      </header>
      ${archiveNotice}
      <div class="layout-grid">
      <div class="main-column">
        ${content}
      </div>
      ${sidebar}
      </div>
    </div>
  </main>
</body>
</html>`;
}

function renderPostCard(post) {
  const categoryLinks = post.categories
    .map((category) => `<a href="/categories/${category.slug}/">${escapeHtml(category.name)}</a>`)
    .join(" · ");

  return `<article class="post-card">
  <p class="post-card__meta">${formatDate(post.date)}</p>
  <h2 class="post-card__title"><a href="${post.url}">${escapeHtml(post.title)}</a></h2>
  <p class="post-card__excerpt">${escapeHtml(post.summary)}</p>
  <p class="post-card__footer">
    <span>${post.comments.length} תגובות</span>
    ${categoryLinks ? `<span>${categoryLinks}</span>` : ""}
  </p>
</article>`;
}

function renderPagination(basePath, pageIndex, pageCount) {
  if (pageCount <= 1) {
    return "";
  }

  const links = [];
  for (let index = 0; index < pageCount; index += 1) {
    const href = index === 0 ? basePath : `${basePath}page/${index + 1}/`;
    links.push(
      `<a class="${index === pageIndex ? "is-active" : ""}" href="${href}">${index + 1}</a>`
    );
  }

  return `<nav class="pagination" aria-label="דפדוף עמודים">${links.join("")}</nav>`;
}

function renderPostPage(post, sidebarData) {
  const categories = post.categories
    .map((category) => `<a href="/categories/${category.slug}/">${escapeHtml(category.name)}</a>`)
    .join(" · ");

  const postNav = post.previousPost || post.nextPost
    ? `<nav class="post-nav" aria-label="פוסט קודם או הבא">
  ${post.previousPost
    ? `<a class="post-nav__link post-nav__link--previous" href="${post.previousPost.url}">
    <span class="post-nav__label">הקודם</span>
    <strong>${escapeHtml(post.previousPost.title)}</strong>
  </a>`
    : `<span class="post-nav__spacer" aria-hidden="true"></span>`}
  ${post.nextPost
    ? `<a class="post-nav__link post-nav__link--next" href="${post.nextPost.url}">
    <span class="post-nav__label">הבא</span>
    <strong>${escapeHtml(post.nextPost.title)}</strong>
  </a>`
    : `<span class="post-nav__spacer" aria-hidden="true"></span>`}
</nav>`
    : "";

  const comments = post.comments.length
    ? `<section class="comments">
  <h2>${post.comments.length} תגובות</h2>
  ${post.comments
    .map(
      (comment) => `<article class="comment">
    <header class="comment__meta">
      <strong>${escapeHtml(comment.author)}</strong>
      <span>${formatDate(comment.date)}</span>
    </header>
    <div class="comment__body">${comment.content}</div>
  </article>`
    )
    .join("")}
</section>`
    : "";

  return layout({
    title: pageTitle(post.title),
    description: post.summary,
    canonical: post.url,
    sidebarData,
    content: `<article class="post-page">
  <header class="post-page__header">
    <p class="post-page__meta">${formatDate(post.date)}</p>
    <h1>${escapeHtml(post.title)}</h1>
    ${categories ? `<p class="post-page__categories">${categories}</p>` : ""}
  </header>
  <div class="post-page__content">
    ${post.contentHtml}
  </div>
  ${postNav}
  ${comments}
</article>`
  });
}

function renderListingPage({ title, intro, posts, basePath, pageIndex, pageCount, sidebarData }) {
  return layout({
    title: pageTitle(title),
    description: intro,
    canonical: pageIndex === 0 ? basePath : `${basePath}page/${pageIndex + 1}/`,
    sidebarData,
    content: `<section class="hero">
  <h1>${escapeHtml(title)}</h1>
  <p>${escapeHtml(intro)}</p>
</section>
<section class="listing">
  ${posts.map(renderPostCard).join("")}
</section>
${renderPagination(basePath, pageIndex, pageCount)}`
  });
}

function renderArchiveIndex(years, sidebarData) {
  return layout({
    title: pageTitle("ארכיון"),
    description: "עיון בכל שנות הפרסום בבלוג",
    canonical: "/archive/",
    sidebarData,
    content: `<section class="hero">
  <h1>ארכיון</h1>
  <p>228 מאמרים שפורסמו בין 11 בספטמבר 2005 לבין 23 ביולי 2013.</p>
</section>
<section class="archive-grid">
  ${years
    .map(
      (year) => `<article class="archive-year">
    <h2><a href="/archive/${year.year}/">${year.year}</a></h2>
    <p>${year.posts.length} מאמרים</p>
    <ul>
      ${year.months
        .map(
          (month) =>
            `<li><a href="/archive/${year.year}/${month.value}/">${month.label}</a> <span>${month.posts.length}</span></li>`
        )
        .join("")}
    </ul>
  </article>`
    )
    .join("")}
</section>`
  });
}

function renderArchivePage({ title, intro, posts, sidebarData }) {
  return layout({
    title: pageTitle(title),
    description: intro,
    canonical: posts.length ? posts[0].archiveUrl : "/archive/",
    sidebarData,
    content: `<section class="hero">
  <h1>${escapeHtml(title)}</h1>
  <p>${escapeHtml(intro)}</p>
</section>
<section class="listing">
  ${posts.map(renderPostCard).join("")}
</section>`
  });
}

function renderCategoriesIndex(categories, sidebarData) {
  return layout({
    title: pageTitle("קטגוריות"),
    description: "כל הקטגוריות מהבלוג המקורי",
    canonical: "/categories/",
    sidebarData,
    content: `<section class="hero">
  <h1>קטגוריות</h1>
  <p>עיון בתוכן לפי נושאים מהבלוג המקורי.</p>
</section>
<section class="categories-grid">
  ${categories
    .map(
      (category) => `<article class="category-card">
    <h2><a href="/categories/${category.slug}/">${escapeHtml(category.name)}</a></h2>
    <p>${category.posts.length} מאמרים</p>
  </article>`
    )
    .join("")}
</section>`
  });
}

function renderStaticPage(page, sidebarData) {
  return layout({
    title: pageTitle(page.title),
    description: page.summary,
    canonical: page.url,
    sidebarData,
    content: `<article class="post-page">
  <header class="post-page__header">
    <h1>${escapeHtml(page.title)}</h1>
  </header>
  <div class="post-page__content">
    ${page.contentHtml}
  </div>
</article>`
  });
}

function renderNotFound(sidebarData) {
  return layout({
    title: pageTitle("העמוד לא נמצא"),
    description: "העמוד המבוקש אינו קיים בארכיון",
    canonical: "/404.html",
    sidebarData,
    content: `<section class="hero">
  <h1>העמוד לא נמצא</h1>
  <p>ייתכן שהקישור שייך למבנה הישן של וורדפרס. נסו את הארכיון או את עמוד הקטגוריות.</p>
</section>`
  });
}

function renderLegacyFallback(sidebarData) {
  return layout({
    title: pageTitle("קישור ישן"),
    description: "דף עזר עבור קישורים ישנים ממבנה וורדפרס המקורי",
    canonical: "/legacy/",
    sidebarData,
    content: `<section class="hero">
  <h1>קישור ישן מהבלוג המקורי</h1>
  <p>הגעתם לקישור ממבנה וורדפרס הישן שלא ניתן היה למפות באופן מלא בזמן הייצוא. השתמשו בארכיון או בקטגוריות כדי להגיע לתוכן הרלוונטי.</p>
</section>
<section class="categories-grid">
  <article class="category-card">
    <h2><a href="/archive/">ארכיון לפי תאריך</a></h2>
    <p>עיון בכל השנים והחודשים של הבלוג.</p>
  </article>
  <article class="category-card">
    <h2><a href="/categories/">עיון לפי קטגוריה</a></h2>
    <p>רשימת הקטגוריות שהועברו לארכיון הסטטי.</p>
  </article>
</section>`
  });
}

function buildStyles() {
  return `:root {
  --bg: #eeeeee;
  --surface: #ffffff;
  --surface-soft: #f9f7f5;
  --ink: #333333;
  --muted: #777777;
  --line: #e2cbac;
  --line-strong: #cccccc;
  --accent: #515815;
  --accent-soft: #f3efe8;
  --heading: #330000;
  --content-width: 770px;
  --reading-width: 858px;
  --shadow: none;
  --radius: 10px;
}

* {
  box-sizing: border-box;
}

html {
  font-size: 17.6px;
}

body {
  margin: 0;
  color: var(--ink);
  background: #eeeeee url("/assets/tile.gif");
  font-family: Arial, Helvetica, sans-serif;
  font-size: 0.76em;
  line-height: 1.7;
}

a {
  color: #969630;
  text-decoration: none;
}

a:hover {
  color: #999900;
  text-decoration: underline;
}

.backdrop {
  display: none;
}

.page-shell {
  width: 100%;
  padding: 1.2rem 0 2rem;
}

.outer-shell {
  width: var(--content-width);
  margin: 0 auto 2em;
  background: var(--surface);
  border: 1px solid var(--line-strong);
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 0 0 1px rgba(255,255,255,0.8) inset;
}

.site-header {
  padding: 0.7rem 0.7rem 0;
}

.site-header__inner {
  padding: 2.5rem 2rem 2.25rem;
  min-height: 160px;
  border-radius: 8px;
  background:
    linear-gradient(rgba(102, 172, 236, 0.58), rgba(102, 172, 236, 0.58)),
    url("/assets/header.jpg") center center / cover no-repeat;
  text-align: center;
}

.site-header__eyebrow,
.post-card__meta,
.post-page__meta,
.comment__meta,
.post-card__footer,
.post-page__categories {
  color: #999999;
  font-size: 14px;
}

.site-title {
  margin: 0;
  font-size: 4em;
  font-weight: bold;
  letter-spacing: 0;
}

.site-title a {
  color: #ffffff;
}

.site-description {
  margin: 0.4rem 0 0;
  font-size: 1.2em;
  color: #ffffff;
  font-weight: bold;
}

.archive-notice {
  margin: 1rem 1.4rem 0;
  padding: 0.85rem 1rem;
  background: #f9f7f5;
  border: 1px solid #e2cbac;
  color: #5a4f2a;
  font-size: 14px;
  line-height: 1.6;
}

.layout-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 204px;
  gap: 0;
  align-items: start;
  direction: ltr;
  margin-top: 0;
}

.main-column {
  min-width: 0;
  direction: rtl;
  padding: 1.15rem 1.2rem 1.35rem 1.2rem;
}

.sidebar {
  position: static;
  display: grid;
  gap: 0.9em;
  direction: rtl;
  padding: 1.35rem 0.8rem 1rem 1rem;
}

.sidebar-card {
  padding: 0;
  background: transparent;
  border: 0;
}

.sidebar-card h2 {
  margin: 0 0 0.5em;
  padding: 0;
  font-size: 1.5em;
  font-weight: normal;
  color: var(--heading);
  border-bottom: 1px solid var(--line);
}

.sidebar-search {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.sidebar-search__label {
  font-weight: bold;
  color: #000;
  font-size: 14px;
}

.sidebar-search__input {
  width: 120px;
  height: 22px;
  border: 1px solid #999;
  background: #fff;
  padding: 2px 4px;
  font: 1em Arial, Helvetica, sans-serif;
}

.sidebar-card--profile {
  text-align: center;
}

.sidebar-profile__image {
  display: block;
  width: 95px;
  height: auto;
  margin: 0 auto 0.7rem;
}

.sidebar-card--profile p {
  margin: 0 0 0.8rem;
  color: #4f4f2b;
  font-size: 14px;
  line-height: 1.45;
  text-align: right;
}

.sidebar-profile__email {
  color: #666;
  font-size: 14px;
}

.sidebar-list {
  margin: 0;
  padding: 0;
  list-style: none;
  line-height: 1.5em;
}

.sidebar-list li {
  display: flex;
  justify-content: space-between;
  gap: 0.8rem;
  padding: 0.2rem 0;
  border-top: 0;
  font-size: 14px;
}

.sidebar-list li:first-child {
  padding-top: 0;
}

.sidebar-list span {
  color: var(--muted);
  white-space: nowrap;
}

.hero,
.post-page,
.post-card,
.archive-year,
.category-card {
  background: transparent;
  border: 0;
  border-radius: 0;
  box-shadow: none;
}

.hero,
.post-page {
  padding: 0;
}

.hero {
  display: none;
}

.hero h1 {
  margin: 0 0 1em;
  font-size: 1.5em;
  font-weight: normal;
  color: var(--heading);
}

.hero p {
  margin: 0;
  color: var(--ink);
}

.listing,
.archive-grid,
.categories-grid {
  display: grid;
  gap: 0.35em;
}

.post-card {
  margin: 0 0 0.8em;
  padding: 0;
}

.post-card__title,
.archive-year h2,
.category-card h2,
.post-page h1,
.comments h2 {
  margin: 0.15rem 0 0.65rem;
  line-height: 1.25;
  color: var(--heading);
  font-weight: normal;
  text-align: right;
}

.post-card__title {
  font-size: 1.7em;
}

.post-card__excerpt {
  margin: 0;
  color: var(--ink);
  font-size: 15px;
  line-height: 1.7;
}

.post-card__footer {
  display: block;
  clear: both;
  padding: 3px 0;
  margin: 0.5em 0 0.35em;
  color: #999999;
  font-size: 15px;
  border-bottom: 1px solid #eeeecc;
}

.pagination {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  margin-top: 1.4rem;
}

.pagination .is-active {
  color: var(--heading);
  font-weight: bold;
  text-decoration: underline;
  background: transparent;
  border: 0;
}

.archive-grid,
.categories-grid {
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.archive-year,
.category-card {
  padding: 0;
}

.archive-year ul {
  margin: 1rem 0 0;
  padding: 0;
  list-style: none;
}

.archive-year li {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.45rem 0;
  border-top: 1px solid var(--line);
}

.archive-year li:first-child {
  border-top: 0;
}

.category-card p,
.archive-year p {
  margin: 0;
  color: var(--muted);
}

.post-page {
  max-width: none;
  margin-inline: 0;
}

.post-page__header {
  margin-bottom: 1em;
}

.post-page h1 {
  font-size: 1.7em;
  line-height: 1.25;
}

.post-page__content {
  margin: 1em 0;
  padding: 0;
  font-size: 15px;
  color: var(--ink);
  line-height: 1.7;
}

.post-page__content > * {
  max-width: 100%;
}

.post-page__content h2,
.post-page__content h3,
.post-page__content h4 {
  margin-top: 2rem;
  margin-bottom: 0.7rem;
  line-height: 1.3;
}

.post-page__content p,
.post-page__content ul,
.post-page__content ol,
.post-page__content blockquote,
.post-page__content pre {
  margin-top: 0;
  margin-bottom: 0.5em;
}

.post-page__content blockquote {
  margin-inline: 0;
  margin: 1em;
  padding: 1em;
  border-right: 2px solid #eeeecc;
  color: #666666;
  background: transparent;
  border-radius: 0;
}

.post-nav {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
  margin-top: 2rem;
}

.post-nav__link,
.post-nav__spacer {
  min-height: 100%;
  padding: 1rem 1.1rem;
  border: 1px solid #cccccc;
  background: var(--surface-soft);
}

.post-nav__link {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  color: var(--ink);
}

.post-nav__link:hover {
  background: #ffffff;
  text-decoration: none;
}

.post-nav__label {
  color: var(--muted);
  font-size: 0.84rem;
}

.post-nav__link--next {
  text-align: left;
}

.post-page__content img,
.comment__body img {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 1.5rem auto;
}

.post-page__content iframe {
  max-width: 100%;
}

.post-page__content pre,
.post-page__content code {
  direction: ltr;
  unicode-bidi: embed;
}

.comments {
  margin-top: 2.5rem;
  padding-top: 1.25rem;
  border-top: 1px solid #cccccc;
}

.comment {
  padding: 1rem 0 1.1rem;
  border-top: 1px solid #cccccc;
}

.comment:first-of-type {
  border-top: 0;
}

.comment__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 0.45rem;
}

.comment__body {
  color: #334155;
  font-size: 15px;
  line-height: 1.7;
}

@media (max-width: 900px), (hover: none) and (pointer: coarse) {
  html {
    font-size: 19px;
  }

  body {
    font-size: 1em;
    line-height: 1.78;
  }

  .site-header {
    padding-top: 1.2rem;
  }

  .page-shell {
    padding-top: 1rem;
  }

  .layout-grid {
    grid-template-columns: 1fr;
  }

  .sidebar {
    position: static;
    order: 2;
    padding: 0 1.1rem 1.1rem;
  }

  .outer-shell {
    width: calc(100% - 1rem);
  }

  .archive-grid,
  .categories-grid {
    grid-template-columns: 1fr;
    gap: 1.2rem;
  }

  .post-card {
    padding: 1rem 0;
  }

  .post-card__excerpt,
  .post-page__content,
  .sidebar-list li,
  .sidebar-card--profile p,
  .sidebar-profile__email,
  .sidebar-search__label,
  .post-card__meta,
  .post-page__meta,
  .post-card__footer,
  .post-page__categories,
  .archive-notice,
  .comment__body {
    font-size: 18px;
  }

  .post-nav {
    grid-template-columns: 1fr;
  }

  .hero,
  .post-page,
  .archive-year,
  .category-card {
    padding: 0;
  }

  .site-header__inner {
    padding: 1.15rem;
  }

  .main-column {
    padding: 0.6rem 0.75rem 1rem;
  }

  .site-title {
    font-size: 3.2em;
  }

  .site-header__inner {
    min-height: 130px;
    padding: 2rem 1rem 1.7rem;
  }

  .archive-notice {
    margin: 0.8rem 1rem 0;
  }

  .sidebar-search__input {
    width: 100%;
    max-width: 150px;
    height: 28px;
  }
}`;
}

function main() {
  cleanOutput();

  const taxonomyMap = new Map(parseWpCategories(XML).map((category) => [category.slug, category.name]));
  const taxonomyById = new Map(parseWpCategories(XML).map((category) => [category.id, category.slug]));
  const items = parseItems(XML, taxonomyMap);
  const posts = items
    .filter((item) => item.postType === "post" && item.status === "publish")
    .sort((left, right) => right.date.localeCompare(left.date));
  const pages = items
    .filter((item) => item.postType === "page" && item.status === "publish")
    .sort((left, right) => left.date.localeCompare(right.date));
  const attachments = items.filter((item) => item.postType === "attachment");

  const postUrlsById = new Map();
  const attachmentUrlsById = new Map();
  const categoryUrlsById = new Map();

  for (const post of posts) {
    post.url = postUrl(post);
    post.archiveUrl = `/archive/${post.date.slice(0, 4)}/${post.date.slice(5, 7)}/`;
    postUrlsById.set(post.id, post.url);
  }

  for (let index = 0; index < posts.length; index += 1) {
    posts[index].nextPost = index > 0 ? posts[index - 1] : null;
    posts[index].previousPost = index < posts.length - 1 ? posts[index + 1] : null;
  }

  for (const page of pages) {
    page.url = `/page/${page.slug}/`;
    postUrlsById.set(page.id, page.url);
  }

  for (const attachment of attachments) {
    attachmentUrlsById.set(attachment.id, attachmentUrl(attachment));
  }

  for (const [id, slug] of taxonomyById.entries()) {
    categoryUrlsById.set(id, `/categories/${slug}/`);
  }

  const rewriteContext = { postUrlsById, attachmentUrlsById, categoryUrlsById };

  for (const post of posts) {
    post.contentHtml = formatContentHtml(rewriteContent(post.content, rewriteContext));
    post.summary = buildExcerpt(post.excerpt || post.contentHtml);
    post.comments = post.comments.map((comment) => ({
      ...comment,
      content: rewriteContent(comment.content, rewriteContext)
    }));
  }

  for (const page of pages) {
    page.contentHtml = formatContentHtml(rewriteContent(page.content, rewriteContext));
    page.summary = buildExcerpt(page.excerpt || page.contentHtml);
  }

  const categories = Array.from(
    posts.reduce((map, post) => {
      for (const category of post.categories) {
        const existing = map.get(category.slug) || { slug: category.slug, name: category.name, posts: [] };
        existing.posts.push(post);
        map.set(category.slug, existing);
      }
      return map;
    }, new Map()).values()
  ).sort((left, right) => right.posts.length - left.posts.length || left.name.localeCompare(right.name, "he"));

  const years = Array.from(
    posts.reduce((map, post) => {
      const [year, month] = post.date.split(" ")[0].split("-");
      const yearEntry = map.get(year) || { year, posts: [], months: new Map() };
      const monthEntry = yearEntry.months.get(month) || {
        value: month,
        label: new Intl.DateTimeFormat("he-IL", { month: "long" }).format(new Date(`2000-${month}-01T00:00:00Z`)),
        posts: []
      };

      yearEntry.posts.push(post);
      monthEntry.posts.push(post);
      yearEntry.months.set(month, monthEntry);
      map.set(year, yearEntry);
      return map;
    }, new Map()).values()
  )
    .sort((left, right) => right.year.localeCompare(left.year))
    .map((year) => ({
      ...year,
      months: Array.from(year.months.values()).sort((left, right) => right.value.localeCompare(left.value))
    }));

  const monthArchive = years.flatMap((year) =>
    year.months.map((month) => ({
      ...month,
      year: year.year
    }))
  );

  const sidebarData = {
    categories,
    monthArchive,
    totalPosts: posts.length,
    totalComments: posts.reduce((sum, post) => sum + post.comments.length, 0)
  };

  writeFile("styles/site.css", buildStyles());
  copyIfExists(path.join(BLOG_DIR, "wp-content", "uploads"), path.join(OUTPUT_DIR, "wp-content", "uploads"));
  copyIfExists(path.join(BLOG_DIR, "blog_images"), path.join(OUTPUT_DIR, "blog_images"));
  copyIfExists(
    path.join(BLOG_DIR, "wp-content", "themes", "justsimple-10", "img", "tile.gif"),
    path.join(OUTPUT_DIR, "assets", "tile.gif")
  );
  copyIfExists(
    path.join(ROOT, "header-banner.png"),
    path.join(OUTPUT_DIR, "assets", "header.jpg")
  );

  const homePages = paginate(posts, POSTS_PER_PAGE);
  homePages.forEach((pagePosts, index) => {
    const html = renderListingPage({
      title: "עיצוב שמיש",
      intro: "ארכיון סטטי של הבלוג המקורי, כולל מאמרים, תגובות וקטגוריות.",
      posts: pagePosts,
      basePath: "/",
      pageIndex: index,
      pageCount: homePages.length,
      sidebarData
    });

    const relativePath = index === 0 ? "index.html" : `page/${index + 1}/index.html`;
    writeFile(relativePath, html);
  });

  for (const post of posts) {
    writeFile(path.join(post.url, "index.html"), renderPostPage(post, sidebarData));
  }

  for (const page of pages) {
    writeFile(path.join(page.url, "index.html"), renderStaticPage(page, sidebarData));
  }

  writeFile("archive/index.html", renderArchiveIndex(years, sidebarData));

  for (const year of years) {
    writeFile(
      `archive/${year.year}/index.html`,
      renderArchivePage({
        title: `ארכיון ${year.year}`,
        intro: `${year.posts.length} מאמרים פורסמו בשנת ${year.year}.`,
        posts: year.posts.map((post) => ({ ...post, archiveUrl: `/archive/${year.year}/` })),
        sidebarData
      })
    );

    for (const month of year.months) {
      writeFile(
        `archive/${year.year}/${month.value}/index.html`,
        renderArchivePage({
          title: `${month.label} ${year.year}`,
          intro: `${month.posts.length} מאמרים פורסמו בחודש זה.`,
          posts: month.posts.map((post) => ({
            ...post,
            archiveUrl: `/archive/${year.year}/${month.value}/`
          })),
          sidebarData
        })
      );
    }
  }

  writeFile("categories/index.html", renderCategoriesIndex(categories, sidebarData));

  for (const category of categories) {
    writeFile(
      `categories/${category.slug}/index.html`,
      renderArchivePage({
        title: category.name,
        intro: `${category.posts.length} מאמרים בקטגוריה זו.`,
        posts: category.posts.map((post) => ({
          ...post,
          archiveUrl: `/categories/${category.slug}/`
        })),
        sidebarData
      })
    );
  }

  writeFile("404.html", renderNotFound(sidebarData));
  writeFile("legacy/index.html", renderLegacyFallback(sidebarData));

  const summary = {
    generatedPosts: posts.length,
    generatedPages: pages.length,
    generatedCategories: categories.length,
    generatedYears: years.length
  };

  fs.writeFileSync(path.join(OUTPUT_DIR, "summary.json"), JSON.stringify(summary, null, 2));
  console.log(JSON.stringify(summary, null, 2));
}

main();
