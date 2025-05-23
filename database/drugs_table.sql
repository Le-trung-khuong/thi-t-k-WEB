-- Thêm bảng Drugs vào database hiện có
USE CommunityHealthDB;
GO

-- Tạo bảng Drugs để lưu thông tin về các loại ma túy
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'Drugs')
BEGIN
    CREATE TABLE Drugs (
        DrugID INT IDENTITY(1,1) PRIMARY KEY,
        DrugName NVARCHAR(255) NOT NULL,
        DrugType NVARCHAR(100) NOT NULL,
        Description NVARCHAR(MAX),
        ImageURL NVARCHAR(500),
        Effects NVARCHAR(MAX),
        ShortTerm NVARCHAR(MAX),
        LongTerm NVARCHAR(MAX),
        PreventionTips NVARCHAR(MAX),
        CreatedAt DATETIME NOT NULL DEFAULT GETDATE(),
        UpdatedAt DATETIME NOT NULL DEFAULT GETDATE()
    );
END
GO

-- Thêm dữ liệu mẫu
IF (SELECT COUNT(*) FROM Drugs) = 0
BEGIN
    -- Ma túy tổng hợp
    INSERT INTO Drugs (DrugName, DrugType, Description, ImageURL, Effects, ShortTerm, LongTerm, PreventionTips)
    VALUES 
    (N'Methamphetamine (Ma túy đá)', N'Ma túy tổng hợp', 
     N'Là loại ma túy dạng tinh thể như đá, tác động mạnh vào hệ thống thần kinh trung ương.', 
     N'https://cdnimg.vietnamplus.vn/uploaded/ngtnnn/2019_07_22/ma_tuy_da.jpg', 
     N'Ảo giác, Kích thích thần kinh, Tăng huyết áp, Mất kiểm soát', 
     N'Tăng huyết áp, nhịp tim nhanh; Tăng năng lượng, hưng phấn; Giảm cảm giác đói; Mất ngủ nghiêm trọng; Hành vi bốc đồng, hung hăng', 
     N'Tổn thương não vĩnh viễn; Suy nhược cơ thể; Mất răng, tổn thương da; Loạn thần, ảo giác; Rối loạn tâm thần nặng', 
     N'Giáo dục về tác hại; Nâng cao nhận thức; Tránh xa môi trường tiếp xúc');
    
    INSERT INTO Drugs (DrugName, DrugType, Description, ImageURL, Effects, ShortTerm, LongTerm, PreventionTips)
    VALUES 
    (N'Ecstasy (Thuốc lắc)', N'Ma túy tổng hợp', 
     N'Thuốc lắc là một loại ma túy tổng hợp, thường được sử dụng tại các quán bar, vũ trường.', 
     N'https://suckhoedoisong.qltns.mediacdn.vn/Images/haiyen/2019/10/22/thuoc_lac_SKCB.jpg', 
     N'Ảo giác, Tăng khoái cảm, Mất nước, Tăng nhịp tim', 
     N'Cảm giác hưng phấn cực độ; Tăng năng lượng; Ảo giác; Tăng thân nhiệt; Mất nước', 
     N'Tổn thương não; Rối loạn tâm thần; Trầm cảm; Suy giảm trí nhớ; Suy kiệt cơ thể', 
     N'Hiểu rõ tác hại; Tránh các quán bar, vũ trường có nguy cơ cao');

    -- Thuốc phiện và dẫn xuất
    INSERT INTO Drugs (DrugName, DrugType, Description, ImageURL, Effects, ShortTerm, LongTerm, PreventionTips)
    VALUES 
    (N'Heroin', N'Thuốc phiện và dẫn xuất', 
     N'Ma túy dạng bột được chiết xuất từ nhựa cây thuốc phiện, gây nghiện mạnh.', 
     N'https://soyte.namdinh.gov.vn/images/2020Anh/T5/heroin-1.jpg', 
     N'Gây ngủ, Giảm đau, Hôn mê, Suy hô hấp, Ngừng tim', 
     N'Cảm giác phê, hưng phấn nhanh; Buồn nôn, nôn mửa; Ngứa, khô miệng; Mơ màng, lơ mơ; Suy hô hấp', 
     N'Tắc mạch máu, suy tim; Nhiễm trùng van tim; Viêm gan B, C và HIV (khi dùng chung kim tiêm); Suy giảm chức năng gan; Cai nghiện đau đớn', 
     N'Tránh xa các khu vực buôn bán ma túy; Nâng cao ý thức cộng đồng');

    -- Cần sa và các sản phẩm
    INSERT INTO Drugs (DrugName, DrugType, Description, ImageURL, Effects, ShortTerm, LongTerm, PreventionTips)
    VALUES 
    (N'Cần sa', N'Cần sa và các sản phẩm', 
     N'Được chiết xuất từ cây cần sa, chứa chất THC gây ảo giác và tác động tâm thần.', 
     N'https://file.medinet.gov.vn/ImagesPosts/can-sa-la-gi-nguyen-nhan-va-dau-hieu-nhan-biet-nghien-can-sa.jpg', 
     N'Hưng phấn tạm thời, Lo âu, Mất trí nhớ ngắn hạn, Rối loạn nhận thức', 
     N'Cảm giác thư giãn; Ảo giác nhẹ; Đói; Khô miệng; Đỏ mắt', 
     N'Suy giảm trí nhớ; Rối loạn phát triển não (ở người trẻ); Rối loạn hô hấp; Tăng nguy cơ bệnh tim; Rối loạn tâm thần', 
     N'Giáo dục thanh thiếu niên; Xây dựng môi trường sống lành mạnh');

    -- Chất gây nghiện theo toa
    INSERT INTO Drugs (DrugName, DrugType, Description, ImageURL, Effects, ShortTerm, LongTerm, PreventionTips)
    VALUES 
    (N'Oxycodone', N'Chất gây nghiện theo toa', 
     N'Thuốc giảm đau mạnh từ nhóm opioid, thường được kê đơn nhưng có nguy cơ gây nghiện cao.', 
     N'https://vnn-imgs-a1.vgcloud.vn/icdn.dantri.com.vn/2021/05/22/thuoc-bao-che-tu-ma-tuy-960-1621654116094.jpeg', 
     N'Giảm đau, Phụ thuộc thuốc, Ngộ độc gan, Suy thận, Trầm cảm', 
     N'Giảm đau; Hưng phấn nhẹ; Buồn ngủ; Chóng mặt; Táo bón', 
     N'Phụ thuộc thuốc; Ngộ độc gan; Suy thận; Rối loạn hô hấp; Quá liều dẫn đến tử vong', 
     N'Tuân thủ đơn thuốc; Không chia sẻ thuốc với người khác; Báo cáo tác dụng phụ với bác sĩ');
END
GO

-- Tạo stored procedure để lấy danh sách ma túy
IF EXISTS (SELECT * FROM sys.procedures WHERE name = 'sp_GetDrugsList')
    DROP PROCEDURE sp_GetDrugsList;
GO

CREATE PROCEDURE sp_GetDrugsList
AS
BEGIN
    SELECT DrugID, DrugName, DrugType, Description, ImageURL, Effects, ShortTerm, LongTerm 
    FROM Drugs
    ORDER BY DrugType, DrugName;
END
GO

-- Tạo stored procedure để lấy thông tin ma túy theo ID
IF EXISTS (SELECT * FROM sys.procedures WHERE name = 'sp_GetDrugById')
    DROP PROCEDURE sp_GetDrugById;
GO

CREATE PROCEDURE sp_GetDrugById
    @DrugID INT
AS
BEGIN
    SELECT * FROM Drugs WHERE DrugID = @DrugID;
END
GO

-- Tạo stored procedure để lấy thông tin ma túy theo tên
IF EXISTS (SELECT * FROM sys.procedures WHERE name = 'sp_GetDrugByName')
    DROP PROCEDURE sp_GetDrugByName;
GO

CREATE PROCEDURE sp_GetDrugByName
    @DrugName NVARCHAR(255)
AS
BEGIN
    SELECT * FROM Drugs WHERE DrugName LIKE '%' + @DrugName + '%';
END
GO

-- Tạo stored procedure để lấy các loại ma túy theo nhóm
IF EXISTS (SELECT * FROM sys.procedures WHERE name = 'sp_GetDrugsByType')
    DROP PROCEDURE sp_GetDrugsByType;
GO

CREATE PROCEDURE sp_GetDrugsByType
AS
BEGIN
    SELECT DrugType, COUNT(*) as Count FROM Drugs GROUP BY DrugType;
END
GO 