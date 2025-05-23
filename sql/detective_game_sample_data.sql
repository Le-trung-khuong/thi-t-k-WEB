-- Đảm bảo dữ liệu được chèn vào đúng database
USE web_k91;

-- Tắt tạm thời ràng buộc khóa ngoại để dễ dàng chèn dữ liệu
SET FOREIGN_KEY_CHECKS = 0;

-- Chèn dữ liệu mẫu cho vụ án "Bóng Ma Trong Trường"

-- Chèn vụ án
INSERT INTO cases (id, title, description, type, difficulty, mode, is_locked, image, timed, time_limit, success_content, failure_content)
VALUES ('drug-school-1', 'Bóng Ma Trong Trường', 'Điều tra việc lan truyền chất gây nghiện trong một trường học. Học sinh có dấu hiệu bất thường và bạn cần tìm ra nguồn gốc và ngăn chặn kịp thời.', 'Ma túy', 1, 'story', FALSE, '../images/cases/school.jpg', FALSE, 0, 
        '<p>Chúc mừng! Bạn đã giải quyết thành công vụ án. Với thông tin từ Tuấn và bằng chứng thu thập được, cảnh sát đã bắt giữ được người cung cấp ma túy và ngăn chặn nguồn cung cấp chất cấm vào trường học.</p><p>Nhà trường đã tổ chức các buổi tuyên truyền về tác hại của ma túy và các chất gây nghiện. Các học sinh liên quan được tư vấn tâm lý và hỗ trợ cai nghiện.</p><p>Nhờ sự điều tra kịp thời của bạn, môi trường học đường đã trở nên an toàn hơn, và các trường học khác trong khu vực cũng được cảnh báo về phương thức hoạt động của bọn buôn bán ma túy.</p>', 
        '<p>Rất tiếc, bạn chưa giải quyết triệt để vấn đề. Người cung cấp chất cấm vẫn tiếp tục hoạt động, và tình trạng sử dụng chất gây nghiện trong trường vẫn diễn ra.</p><p>Điều này cho thấy tầm quan trọng của việc xử lý các vụ việc liên quan đến ma túy một cách chuyên nghiệp và phối hợp với các cơ quan chức năng, đặc biệt khi nó liên quan đến môi trường học đường.</p>');

-- Chèn vụ án thứ 2 (đang bị khóa)
INSERT INTO cases (id, title, description, type, difficulty, mode, is_locked, image, timed, time_limit)
VALUES ('alcohol-family-1', 'Bí Mật Gia Đình', 'Điều tra một vụ tai nạn giao thông và khám phá bí mật về vấn đề nghiện rượu giấu kín trong một gia đình tưởng chừng hạnh phúc.', 'Rượu bia', 2, 'story', TRUE, '../images/cases/family.jpg', FALSE, 0);

-- Chèn vụ án thứ 3 (cho next_case_id)
INSERT INTO cases (id, title, description, type, difficulty, mode, is_locked, image, timed, time_limit)
VALUES ('drug-network-1', 'Đường Dây Ma Túy', 'Điều tra và triệt phá một đường dây ma túy lớn đang nhắm vào học sinh nhiều trường trong khu vực.', 'Ma túy', 3, 'story', TRUE, '../images/cases/network.jpg', FALSE, 0);

-- Chèn điều kiện mở khóa cho vụ án thứ 2
INSERT INTO case_requirements (case_id, required_case_id)
VALUES ('alcohol-family-1', 'drug-school-1');

-- Chèn bằng chứng
INSERT INTO evidence (id, case_id, name, description, image, analysis, points, collected_by_default)
VALUES ('drug-school-1-evidence-1', 'drug-school-1', 'Viên nén lạ', 'Một viên nén màu hồng với logo hình trái tim, tìm thấy trong nhà vệ sinh.', '../images/evidence/drug-pill.jpg', 'Đây là loại ma túy tổng hợp ecstasy, thường được sử dụng tại các buổi tiệc và gây ảo giác, tăng cảm xúc.', 10, FALSE),
       ('drug-school-1-evidence-2', 'drug-school-1', 'Tin nhắn đáng ngờ', 'Một loạt tin nhắn từ số lạ về việc "hàng mới" và "giao dịch sau giờ học".', '../images/evidence/messages.jpg', 'Sử dụng mã và ngôn ngữ ẩn dụ để trao đổi về việc mua bán chất cấm.', 15, FALSE),
       ('drug-school-1-evidence-3', 'drug-school-1', 'Danh sách học sinh', 'Danh sách học sinh với một số tên được đánh dấu bằng ký hiệu đặc biệt.', '../images/evidence/student-list.jpg', 'Đây có thể là danh sách khách hàng hoặc người phân phối trong mạng lưới.', 10, FALSE),
       ('drug-school-1-evidence-4', 'drug-school-1', 'Túi nilon nhỏ', 'Nhiều túi nilon nhỏ trong thùng rác, thường được dùng để đựng ma túy.', '../images/evidence/plastic-bags.jpg', 'Dụng cụ đóng gói chất cấm điển hình, cho thấy có hoạt động phân phối tại trường.', 5, FALSE),
       ('drug-school-1-evidence-5', 'drug-school-1', 'Báo cáo y tế', 'Báo cáo từ phòng y tế trường về việc nhiều học sinh có triệu chứng khác thường.', '../images/evidence/medical-report.jpg', 'Các triệu chứng khớp với tác động của ma túy tổng hợp: tim đập nhanh, mất nước, tăng nhiệt độ cơ thể.', 20, FALSE);

-- Chèn địa điểm
INSERT INTO locations (id, case_id, name, description, image, first_location, locked)
VALUES ('drug-school-1-location-1', 'drug-school-1', 'Phòng Hiệu Trưởng', 'Nơi bạn nhận nhiệm vụ điều tra từ hiệu trưởng về tình trạng bất thường của học sinh.', '../images/locations/principal-office.jpg', TRUE, FALSE),
       ('drug-school-1-location-2', 'drug-school-1', 'Sân trường', 'Nơi học sinh tụ tập trong giờ ra chơi. Bạn nhận thấy một nhóm học sinh có hành vi khả nghi ở góc xa của sân.', '../images/locations/school-yard.jpg', FALSE, FALSE),
       ('drug-school-1-location-3', 'drug-school-1', 'Nhà vệ sinh học sinh', 'Bạn nghe nói có hoạt động đáng ngờ diễn ra trong đây. Khi kiểm tra, bạn thấy vài viên nén lạ rơi trong góc.', '../images/locations/school-bathroom.jpg', FALSE, FALSE),
       ('drug-school-1-location-4', 'drug-school-1', 'Phòng tủ đồ', 'Phòng tủ đồ của học sinh. Có vài tủ bị khóa bất thường và có dấu hiệu bị động chạm gần đây.', '../images/locations/school-lockers.jpg', FALSE, FALSE),
       ('drug-school-1-location-5', 'drug-school-1', 'Phòng y tế', 'Nơi chăm sóc sức khỏe học sinh. Bạn gặp y tá trường và xem xét các báo cáo gần đây.', '../images/locations/school-nurse.jpg', FALSE, TRUE),
       ('drug-school-1-location-6', 'drug-school-1', 'Cổng trường', 'Khu vực ra vào trường học. Bạn quan sát những người thường xuyên xuất hiện gần cổng trường vào giờ tan học.', '../images/locations/school-gate.jpg', FALSE, TRUE);

-- Chèn điều kiện mở khóa địa điểm
INSERT INTO location_requirements (location_id, required_evidence_id)
VALUES ('drug-school-1-location-5', 'drug-school-1-evidence-1'),
       ('drug-school-1-location-6', 'drug-school-1-evidence-2'),
       ('drug-school-1-location-6', 'drug-school-1-evidence-3');

-- Chèn hành động địa điểm
INSERT INTO location_actions (location_id, action_type, target_id, label, icon)
VALUES ('drug-school-1-location-2', 'talk-to-character', 'drug-school-1-character-1', 'Nói chuyện với học sinh', 'fa-comment'),
       ('drug-school-1-location-2', 'examine-location', 'drug-school-1-location-3', 'Kiểm tra nhà vệ sinh', 'fa-search'),
       ('drug-school-1-location-3', 'collect-evidence', 'drug-school-1-evidence-1', 'Thu thập viên nén', 'fa-hand-paper'),
       ('drug-school-1-location-3', 'collect-evidence', 'drug-school-1-evidence-4', 'Kiểm tra thùng rác', 'fa-trash'),
       ('drug-school-1-location-3', 'examine-location', 'drug-school-1-location-4', 'Kiểm tra phòng tủ đồ', 'fa-search'),
       ('drug-school-1-location-4', 'talk-to-character', 'drug-school-1-character-2', 'Nói chuyện với giám thị', 'fa-comment'),
       ('drug-school-1-location-4', 'collect-evidence', 'drug-school-1-evidence-3', 'Kiểm tra danh sách học sinh', 'fa-file-alt'),
       ('drug-school-1-location-5', 'talk-to-character', 'drug-school-1-character-4', 'Nói chuyện với y tá', 'fa-comment'),
       ('drug-school-1-location-5', 'collect-evidence', 'drug-school-1-evidence-5', 'Xem báo cáo y tế', 'fa-file-medical'),
       ('drug-school-1-location-6', 'talk-to-character', 'drug-school-1-character-3', 'Theo dõi người đáng ngờ', 'fa-eye');

-- Chèn nhân vật
INSERT INTO characters (id, case_id, name, role, image, initial_dialog, locked)
VALUES ('drug-school-1-character-1', 'drug-school-1', 'Học sinh Minh', 'Học sinh lớp 11A', '../images/characters/student1.jpg', 'Em không biết gì đâu... Những đứa khác đang dùng thứ gì đó trong nhà vệ sinh, nhưng em không tham gia. Có vẻ như chúng đang ngày càng phổ biến trong trường.', FALSE),
       ('drug-school-1-character-2', 'drug-school-1', 'Giám thị Hùng', 'Giám thị trường', '../images/characters/supervisor.jpg', 'Tôi làm việc ở đây 5 năm rồi và chưa bao giờ thấy tình trạng này. Gần đây một số học sinh có biểu hiện rất lạ - hưng phấn quá mức rồi đột ngột mệt mỏi, thờ ơ.', FALSE),
       ('drug-school-1-character-3', 'drug-school-1', 'Tuấn - Học sinh', 'Nghi phạm', '../images/characters/student2.jpg', 'Tôi không hiểu sao lại bị gọi đến đây. Tôi không liên quan gì đến những chuyện này cả.', TRUE),
       ('drug-school-1-character-4', 'drug-school-1', 'Y tá Hương', 'Y tá trường học', '../images/characters/nurse.jpg', 'Tôi đã ghi nhận 12 trường hợp học sinh có triệu chứng bất thường trong tháng qua - tim đập nhanh, sốt nhẹ, mất nước, và đặc biệt là trạng thái tâm lý thay đổi đột ngột.', FALSE),
       ('drug-school-1-character-5', 'drug-school-1', 'Người đàn ông lạ', 'Kẻ cung cấp ma túy', '../images/characters/dealer.jpg', 'Này, tôi chỉ đang đợi một người bạn thôi. Tôi không làm gì sai cả.', TRUE);

-- Chèn điều kiện mở khóa nhân vật
INSERT INTO character_requirements (character_id, requirement_type, required_id)
VALUES ('drug-school-1-character-3', 'evidence', 'drug-school-1-evidence-2'),
       ('drug-school-1-character-3', 'evidence', 'drug-school-1-evidence-3'),
       ('drug-school-1-character-5', 'evidence', 'drug-school-1-evidence-2'),
       ('drug-school-1-character-5', 'evidence', 'drug-school-1-evidence-5'),
       ('drug-school-1-character-5', 'character', 'drug-school-1-character-3');

-- Chèn lựa chọn đối thoại
INSERT INTO dialog_options (id, character_id, text, correct, points, response, is_final_solution)
VALUES ('drug-school-1-dialog-1-1', 'drug-school-1-character-1', 'Thầy/cô không buộc tội em. Thầy/cô chỉ cần thông tin để giúp các bạn.', TRUE, 10, 'Em... em có thấy Tuấn hay nhận tin nhắn lạ. Cậu ấy cũng hay nói về "hàng mới". Em nghĩ điện thoại cậu ấy có thể có manh mối. Thầy/cô nên kiểm tra nhà vệ sinh, em thấy có gì đó đáng ngờ ở đó.', FALSE),
       ('drug-school-1-dialog-1-2', 'drug-school-1-character-1', 'Nói dối sẽ khiến em gặp rắc rối đấy. Tốt nhất em nên nói ra những gì em biết.', FALSE, 0, 'Em thực sự không biết gì nhiều... Thầy/cô nên tìm Tuấn ấy. Cậu ấy có thể biết nhiều hơn. Nhưng em không muốn dính líu...', FALSE),
       ('drug-school-1-dialog-2-1', 'drug-school-1-character-2', 'Ông có thể cho tôi xem danh sách các học sinh có biểu hiện bất thường không?', TRUE, 10, 'Được chứ. Tôi đã đánh dấu những em có biểu hiện lạ trong vài tuần qua. Có thể giúp ích cho cuộc điều tra của bạn. Tôi cũng đã báo cáo với y tá trường, cô ấy có thể có thêm thông tin.', FALSE),
       ('drug-school-1-dialog-2-2', 'drug-school-1-character-2', 'Ông có phát hiện ra ai mang những thứ lạ vào trường không?', FALSE, 5, 'Tôi không để ý nhiều... Nhưng tôi có thấy một vài học sinh hay tụ tập ở góc sân trường, khuất tầm nhìn của camera giám sát. Điều đáng ngờ là chúng thường nhận tin nhắn và sau đó ra gặp ai đó ở cổng trường.', FALSE),
       ('drug-school-1-dialog-3-1', 'drug-school-1-character-3', 'Tuấn, chúng tôi tìm thấy tin nhắn và bằng chứng. Hợp tác sẽ giúp em giảm nhẹ hậu quả.', TRUE, 20, 'Được rồi... Em sẽ nói. Có một người đàn ông thường đỗ xe bên ngoài cổng trường vào thứ Sáu. Anh ta cung cấp những viên thuốc này cho một số học sinh, và chúng em bán lại cho các bạn khác. Em... em không nghĩ nó nguy hiểm đến vậy.', FALSE),
       ('drug-school-1-dialog-3-2', 'drug-school-1-character-3', 'Đầu thú ngay nếu không muốn bị đuổi học vĩnh viễn!', FALSE, 0, 'Em không biết gì cả! Em cần gặp phụ huynh và luật sư trước khi nói chuyện tiếp. Các thầy cô không có quyền đối xử với em như thế này!', FALSE),
       ('drug-school-1-dialog-4-1', 'drug-school-1-character-4', 'Cô có nghĩ rằng đây là dấu hiệu của việc sử dụng chất kích thích không?', TRUE, 15, 'Tôi không muốn đưa ra kết luận vội vàng, nhưng các triệu chứng này khá giống với tác động của một số loại ma túy tổng hợp. Tôi đã lập danh sách các em có triệu chứng giống nhau trong báo cáo này.', FALSE),
       ('drug-school-1-dialog-4-2', 'drug-school-1-character-4', 'Có thể họ chỉ bị cúm mùa hoặc áp lực học tập thôi?', FALSE, 5, 'Tôi đã loại trừ các khả năng đó rồi. Triệu chứng xuất hiện và biến mất quá nhanh để là cúm, và thường xảy ra sau giờ ra chơi hoặc sau giờ học. Điều này không bình thường.', FALSE),
       ('drug-school-1-dialog-5-1', 'drug-school-1-character-5', 'Chúng tôi đã có đủ bằng chứng về hoạt động cung cấp ma túy của anh cho học sinh.', TRUE, 30, '[Người đàn ông cố gắng bỏ chạy nhưng bị chặn lại] "Các người không có gì cả! Tôi không..." [Khi thấy cảnh sát xuất hiện, anh ta im lặng]', TRUE),
       ('drug-school-1-dialog-5-2', 'drug-school-1-character-5', 'Nếu anh hợp tác, tôi sẽ báo cáo sự giúp đỡ của anh với tòa án.', FALSE, 15, 'Tôi không biết các anh đang nói gì. [Bất ngờ bỏ chạy nhưng bị cảnh sát bắt giữ sau đó]', TRUE);

-- Chèn mở khóa từ đối thoại
INSERT INTO dialog_unlocks (dialog_option_id, unlock_type, unlocked_id)
VALUES ('drug-school-1-dialog-1-1', 'location', 'drug-school-1-location-3'),
       ('drug-school-1-dialog-2-1', 'location', 'drug-school-1-location-5'),
       ('drug-school-1-dialog-2-1', 'evidence', 'drug-school-1-evidence-3'),
       ('drug-school-1-dialog-3-1', 'location', 'drug-school-1-location-6'),
       ('drug-school-1-dialog-3-1', 'evidence', 'drug-school-1-evidence-2'),
       ('drug-school-1-dialog-4-1', 'evidence', 'drug-school-1-evidence-5');

-- Chèn các bước
INSERT INTO steps (id, case_id, narrative, location_id, is_starting_step, next_step_id)
VALUES ('drug-school-1-step-1', 'drug-school-1', '<h3>Phòng Hiệu Trưởng</h3><p>Hiệu trưởng đã mời bạn đến điều tra về tình trạng một số học sinh có biểu hiện bất thường trong thời gian gần đây. Có nghi vấn về việc sử dụng chất cấm trong trường.</p><p>"Chúng tôi lo ngại về sự an toàn của học sinh," hiệu trưởng nói. "Một số em có biểu hiện thay đổi đáng kể - khi thì hưng phấn quá mức, khi thì mệt mỏi, học lực giảm sút. Làm ơn giúp chúng tôi tìm ra nguyên nhân."</p><p>Bạn quyết định bắt đầu điều tra từ đâu?</p>', 'drug-school-1-location-1', TRUE, NULL);

-- Chèn hành động bước
INSERT INTO step_actions (step_id, action_type, target_id, label, icon)
VALUES ('drug-school-1-step-1', 'examine-location', 'drug-school-1-location-2', 'Khảo sát sân trường', 'fa-search');

-- Chèn bước cuối
INSERT INTO final_steps (id, case_id, require_character_id, narrative)
VALUES ('drug-school-1-final', 'drug-school-1', 'drug-school-1-character-5', '<h3>Giải Quyết Vụ Án</h3><p>Sau khi thu thập đủ bằng chứng từ trường học và thông tin từ học sinh Tuấn, bạn đã xác định được người đàn ông cung cấp ma túy cho học sinh bên ngoài cổng trường.</p><p>Với sự hợp tác của ban giám hiệu và cảnh sát, bạn đã lập kế hoạch bắt giữ kẻ cung cấp ma túy. Giờ đây bạn cần quyết định cách xử lý tình huống này.</p>');

-- Chèn điều kiện bằng chứng cho bước cuối
INSERT INTO final_step_evidence (final_step_id, evidence_id)
VALUES ('drug-school-1-final', 'drug-school-1-evidence-1'),
       ('drug-school-1-final', 'drug-school-1-evidence-2'),
       ('drug-school-1-final', 'drug-school-1-evidence-5');

-- Chèn lựa chọn bước cuối
INSERT INTO final_choices (id, final_step_id, text, correct, points, outcome, next_case_id)
VALUES ('drug-school-1-choice-1', 'drug-school-1-final', 'Phối hợp với cảnh sát để bắt giữ người cung cấp và điều tra mạng lưới phân phối ma túy rộng lớn hơn', TRUE, 30, 'Cảnh sát đã bắt giữ được kẻ cung cấp ma túy và từ thông tin của hắn, triệt phá được một đường dây cung cấp ma túy cho học sinh nhiều trường trong khu vực. Các học sinh liên quan được giáo dục và tư vấn tâm lý.', 'drug-network-1'),
       ('drug-school-1-choice-2', 'drug-school-1-final', 'Xử lý nội bộ trong trường, kỷ luật các học sinh liên quan và cảnh cáo người cung cấp', FALSE, 10, 'Việc xử lý nội bộ không đủ mạnh để ngăn chặn vấn đề. Người cung cấp vẫn tiếp tục hoạt động ở các trường khác, và một số học sinh vẫn bí mật sử dụng chất cấm.', NULL);

-- Chèn kiến thức bổ ích
INSERT INTO educational_tips (case_id, tip_text, order_num)
VALUES ('drug-school-1', 'Ma túy tổng hợp như ecstasy có thể gây ra những tác hại nghiêm trọng đến sức khỏe thể chất và tinh thần, đặc biệt ở người trẻ tuổi.', 1),
       ('drug-school-1', 'Dấu hiệu nhận biết người sử dụng chất kích thích: thay đổi tâm trạng đột ngột, mắt đỏ, đồng tử giãn, mất ngủ, ăn ít, sút cân không rõ nguyên nhân.', 2),
       ('drug-school-1', 'Khi phát hiện người thân có dấu hiệu sử dụng chất cấm, hãy bình tĩnh tiếp cận và tìm sự hỗ trợ từ chuyên gia tâm lý, y tế.', 3),
       ('drug-school-1', 'Phòng ngừa sử dụng ma túy học đường cần kết hợp giáo dục, truyền thông và sự phối hợp giữa nhà trường, gia đình và cộng đồng.', 4),
       ('drug-school-1', 'Nhiều học sinh sử dụng ma túy không nhận thức đầy đủ về tác hại và tính nghiêm trọng của hành vi này.', 5);
       
-- Bật lại ràng buộc khóa ngoại
SET FOREIGN_KEY_CHECKS = 1; achievements