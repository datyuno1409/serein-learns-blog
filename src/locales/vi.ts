export const vi = {
  // Navigation
  nav: {
    home: "Trang chủ",
    articles: "Bài viết",
    about: "Giới thiệu",
    search: "Tìm kiếm",
    createArticle: "Tạo bài viết",
    login: "Đăng nhập",
    logout: "Đăng xuất",
    myProjects: "Dự án của tôi"
  },
  articles: {
    edit: 'Chỉnh sửa',
    delete: 'Xóa',
    deleteConfirmTitle: 'Bạn có chắc chắn?',
    deleteConfirmMessage: 'Hành động này không thể hoàn tác. Bài viết sẽ bị xóa vĩnh viễn.',
    deleteSuccess: 'Thành công',
    deleteSuccessMessage: 'Xóa bài viết thành công',
    deleteError: 'Lỗi',
    deleteErrorMessage: 'Xóa bài viết thất bại',
    minuteRead: 'phút đọc',
    notFound: 'Không tìm thấy bài viết',
    adjustSearch: 'Hãy thử xem tất cả bài viết',
  },
  common: {
    cancel: 'Hủy',
    delete: 'Xóa',
    verify: 'Xác thực',
    viewAll: 'Xem tất cả bài viết',
  },
  auth: {
    unauthorized: 'Không được phép',
    loginRequired: 'Bạn cần đăng nhập để truy cập trang này.',
  },
  createArticle: {
    title: 'Chỉnh sửa bài viết',
    subtitle: 'Cập nhật nội dung và cài đặt bài viết của bạn',
    formTitle: 'Tiêu đề',
    titlePlaceholder: 'Nhập tiêu đề bài viết',
    excerpt: 'Tóm tắt',
    briefSummary: 'Tóm tắt ngắn',
    excerptPlaceholder: 'Nhập tóm tắt ngắn về bài viết của bạn',
    content: 'Nội dung',
    contentPlaceholder: 'Viết nội dung bài viết của bạn',
    coverImage: 'Ảnh bìa',
    coverImagePlaceholder: 'Nhập URL cho ảnh bìa',
    category: 'Danh mục',
    selectCategory: 'Chọn danh mục',
    tags: 'Thẻ',
    commaSeparated: 'phân cách bằng dấu phẩy',
    tagsPlaceholder: 'Ví dụ: JavaScript, Bảo mật, React',
    publish: 'Cập nhật bài viết',
    publishing: 'Đang cập nhật...',
    cancel: 'Hủy',
    missingFields: 'Thiếu thông tin bắt buộc',
    fillRequiredFields: 'Vui lòng điền đầy đủ thông tin bắt buộc.',
    success: 'Đã cập nhật bài viết!',
    successMsg: 'Bài viết của bạn đã được cập nhật thành công.',
  },
  education: {
    title: "Học Vấn",
    university: "Đại học Công nghệ Thông tin - ĐHQG TP.HCM",
    degree: "Kỹ sư Công nghệ Thông tin",
    period: "2019 - 2024",
    achievements: [
      "Thành viên tích cực của Câu lạc bộ Nghiên cứu An toàn Thông tin (Security Research Club)",
      "Đội trưởng đội thi CTF, đạt giải Ba trong cuộc thi ISITDTU CTF 2023",
      "Sinh viên Xuất sắc năm học 2022-2023",
      "Đóng góp tích cực trong việc tổ chức các sự kiện và hội thảo về an toàn thông tin",
      "Đề tài tốt nghiệp: Nghiên cứu và phát triển hệ thống phát hiện tấn công mạng dựa trên học máy"
    ]
  },
  about: {
    name: "NGUYỄN THÀNH ĐẠT",
    title: "KỸ SƯ HỖ TRỢ KỸ THUẬT",
    experience: "1 NĂM KINH NGHIỆM",
    description: "Tìm hiểu thêm về nền tảng, kỹ năng và kinh nghiệm của tôi",
    skills: "Kỹ Năng",
    certifications: "Chứng Chỉ",
    education: "Học Vấn",
    achievements: "Thành Tích Nổi Bật",
    contact: "LIÊN HỆ",
    downloadCV: "Tải CV",
    phone: "Số điện thoại",
    email: "Email",
    address: "Địa chỉ",
    addressValue: "Phường Trường Thọ, Thành phố Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam"
  },
  myProjects: {
    title: "Dự Án Của Tôi",
    subtitle: "Bộ sưu tập các dự án cá nhân và đóng góp cho phần mềm mã nguồn mở",
    create: "Tạo Dự Án",
    edit: "Chỉnh Sửa",
    delete: "Xóa",
    deleteConfirmTitle: "Xóa Dự Án?",
    deleteConfirmMessage: "Hành động này không thể hoàn tác. Dự án sẽ bị xóa vĩnh viễn.",
    deleteSuccess: "Xóa dự án thành công",
    deleteError: "Xóa dự án thất bại",
    form: {
      title: "Tên Dự Án",
      description: "Mô Tả Dự Án",
      image: "URL Hình Ảnh",
      github: "URL GitHub",
      demo: "URL Demo",
      tags: "Công Nghệ Sử Dụng",
      featured: "Dự Án Nổi Bật",
      save: "Lưu Dự Án",
      saving: "Đang Lưu...",
      cancel: "Hủy"
    }
  },
} as const;

export default vi; 