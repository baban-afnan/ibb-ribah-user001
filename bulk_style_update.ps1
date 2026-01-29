$path = 'c:\Users\shafi\OneDrive\Desktop\my website\digitaldataverify\safana-user\public\assets\css\style.css'
(Get-Content $path) -replace '#0d5c3e', '#002fba' `
-replace '#0a4a32', '#002696' `
-replace '#157a53', '#4d6be0' `
-replace '#E6F7F0', '#EAF0FF' `
-replace 'rgba\(13, 92, 62,', 'rgba(0, 47, 186,' `
-replace 'rgb\(13, 92, 62\)', 'rgb(0, 47, 186)' `
-replace 'rgb\(222.1333333333, 80.4102564103, 13.0666666667\)', '#001f7a' `
-replace 'rgb\(240.9696682464, 92.4819905213, 22.3303317536\)', '#0047e1' | Set-Content $path
