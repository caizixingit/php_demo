<%
' ���ܣ������ؽӿڽ���ҳ
' �汾��3.3
' ���ڣ�2012-07-17
' ˵����
' ���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
' �ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���
	
' /////////////////ע��/////////////////
' ������ڽӿڼ��ɹ������������⣬���԰��������;�������
' 1���̻��������ģ�https://b.alipay.com/support/helperApply.htm?action=consultationApply�����ύ���뼯��Э�������ǻ���רҵ�ļ�������ʦ������ϵ��Э�����
' 2���̻��������ģ�http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9��
' 3��֧������̳��http://club.alipay.com/read-htm-tid-8681712.html��
' /////////////////////////////////////

%>
<html>
<head>
	<META http-equiv=Content-Type content="text/html; charset=gb2312">
<title>֧���������ؽӿ�</title>
</head>
<body>

<!--#include file="class/alipay_submit.asp"-->

<%
'/////////////////////�������/////////////////////

        '֧������
        payment_type = "1"
        '��������޸�
        '�������첽֪ͨҳ��·��
        notify_url = "http://�̻����ص�ַ/create_direct_pay_by_user-ASP-GBK/notify_url.asp"
        '��http://��ʽ������·�������ܼ�?id=123�����Զ������

        'ҳ����תͬ��֪ͨҳ��·��
        return_url = "http://�̻����ص�ַ/create_direct_pay_by_user-ASP-GBK/return_url.asp"
        '��http://��ʽ������·�������ܼ�?id=123�����Զ������������д��http://localhost/

        '�̻�������
        out_trade_no = Request.Form("WIDout_trade_no")
        '�̻���վ����ϵͳ��Ψһ�����ţ�����

        '��������
        subject = Request.Form("WIDsubject")
        '����

        '������
        total_fee = Request.Form("WIDtotal_fee")
        '����

        '��������

        body = Request.Form("WIDbody")
        'Ĭ��֧����ʽ
        paymethod = "bankPay"
        '����
        'Ĭ������
        defaultbank = Request.Form("WIDdefaultbank")
        '������м�����ο��ӿڼ����ĵ�

        '��Ʒչʾ��ַ
        show_url = Request.Form("WIDshow_url")
        '����http://��ͷ������·�������磺http://www.�̻���ַ.com/myorder.html

        '������ʱ���
        anti_phishing_key = ""
        '��Ҫʹ����������ļ�submit�е�query_timestamp����

        '�ͻ��˵�IP��ַ
        exter_invoke_ip = ""
        '�Ǿ�����������IP��ַ���磺221.0.0.1

'/////////////////////�������/////////////////////

'���������������
sParaTemp = Array("service=create_direct_pay_by_user","partner="&partner,"seller_email="&seller_email,"_input_charset="&input_charset  ,"payment_type="&payment_type   ,"notify_url="&notify_url   ,"return_url="&return_url   ,"out_trade_no="&out_trade_no   ,"subject="&subject   ,"total_fee="&total_fee   ,"body="&body   ,"paymethod="&paymethod   ,"defaultbank="&defaultbank   ,"show_url="&show_url   ,"anti_phishing_key="&anti_phishing_key   ,"exter_invoke_ip="&exter_invoke_ip  )

'��������
Set objSubmit = New AlipaySubmit
sHtml = objSubmit.BuildRequestForm(sParaTemp, "get", "ȷ��")
response.Write sHtml


%>
</body>
</html>
