// بيانات التحميلات
const downloads = {
    computerPrograms: [
        {
            title: "برنامج 1",
            description: "وصف البرنامج الأول",
            downloadUrl: "#",
            size: "10MB"
        }
    ],
    mobileApps: [
        {
            title: "تطبيق 1",
            description: "وصف التطبيق الأول",
            downloadUrl: "#",
            size: "5MB"
        }
    ],
    tutorials: [
        {
            title: "شرح 1",
            description: "وصف الشرح الأول",
            downloadUrl: "#",
            type: "PDF"
        }
    ],
    designVideos: [
        {
            title: "فيديو 1",
            description: "وصف الفيديو الأول",
            downloadUrl: "#",
            duration: "10:00"
        }
    ]
};

// دالة لإنشاء عنصر تحميل
function createDownloadItem(item, type) {
    const div = document.createElement('div');
    div.className = 'download-item';
    
    let details = '';
    switch(type) {
        case 'program':
            details = `الحجم: ${item.size}`;
            break;
        case 'app':
            details = `الحجم: ${item.size}`;
            break;
        case 'tutorial':
            details = `النوع: ${item.type}`;
            break;
        case 'video':
            details = `المدة: ${item.duration}`;
            break;
    }

    div.innerHTML = `
        <h4>${item.title}</h4>
        <p>${item.description}</p>
        <p class="details">${details}</p>
        <a href="${item.downloadUrl}" class="download-btn" download>
            <i class="fas fa-download"></i> تحميل
        </a>
    `;

    return div;
}

// إضافة عناصر التحميل إلى الصفحة
function initializeDownloads() {
    const computerProgramsContainer = document.getElementById('computer-programs');
    const mobileAppsContainer = document.getElementById('mobile-apps');
    const tutorialsContainer = document.getElementById('tutorials');
    const designVideosContainer = document.getElementById('design-videos');

    downloads.computerPrograms.forEach(program => {
        computerProgramsContainer.appendChild(createDownloadItem(program, 'program'));
    });

    downloads.mobileApps.forEach(app => {
        mobileAppsContainer.appendChild(createDownloadItem(app, 'app'));
    });

    downloads.tutorials.forEach(tutorial => {
        tutorialsContainer.appendChild(createDownloadItem(tutorial, 'tutorial'));
    });

    downloads.designVideos.forEach(video => {
        designVideosContainer.appendChild(createDownloadItem(video, 'video'));
    });
}

// تهيئة الصفحة عند التحميل
document.addEventListener('DOMContentLoaded', initializeDownloads);
