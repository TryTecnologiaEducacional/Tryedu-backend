<?php
    include_once('_private/config.inc.php');

    if (isset($_GET['u']) && isset($_GET['ts']) && isset($_GET['pwd'])){
        $ObjUser = new User();
        $filtro = "TokenPass = '" . $_GET['ts'] . "'";
        $qt = ($ObjUser->listarQuantidade($filtro) ==1) ? true : false;
        if ($qt) {
            $pwd_peppered = hash_hmac("sha256", $_GET['pwd'], pimenta);
            $d['Password'] = password_hash($pwd_peppered, PASSWORD_ARGON2ID);
            $d['TokenPass'] = null;
            $ok = ($ObjUser->atualizar($_GET['u'], $d))? true : false;
            $msg = ($ok)? 'Sua senha foi gravada com sucesso, abra o app e entre com ela.' : 'Lamentamos informar que aconteceu um erro.<br>Nossos técnicos já estão verificando.<br>Tente novamente mais tarde.';
        } else {
            $msg = 'Informações inválidas para recuperação de senha.';
        }
        $texto = '<h2 class="informeDados__LabelTitle-sc-akle90-1 errEjS">' . $msg . '</h2>';
    } else {
        $texto = '<h2 class="informeDados__LabelTitle-sc-akle90-1 errEjS">Informe a nova senha:</h2>
        <div style="display: block;" class="informeDados__ContentInputs-sc-akle90-2 cGyiGr">
        <input type="hidden" value="' . $_GET['u'] . '" name="u" placeholder="Digite a senha" required=true class="informeDados__Input-sc-akle90-4 cxnaMm">
        <input type="hidden" value="' . $_GET['ts'] . '" name="ts" placeholder="Digite a senha" required=true class="informeDados__Input-sc-akle90-4 cxnaMm">
        <label class="informeDados__ContainerInput-sc-akle90-3 eefkls">
            <input type="password" id="password" name="pwd" placeholder="Digite a senha" required=true class="informeDados__Input-sc-akle90-4 cxnaMm">
        </label>
        <label class="informeDados__ContainerInput-sc-akle90-3 eefkls">
            <input type="password" id="password2" placeholder="Digite a senha novamente" required=true class="informeDados__Input-sc-akle90-4 cxnaMm">
        </label></div><div style="display: none;" class="informeDados__ContentInputs-sc-akle90-2 cGyiGr">
        </div>
        <button type="submit" class="reutilizaveis__BtnAvancar-sc-1rpqs51-2 dRzqWT">Avançar<svg width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg" style="float: right; margin: 15px 10px 0px 0px;"><path d="M17.13 7.94l-6.714-7A1.4 1.4 0 009.023.555c-.496.14-.884.546-1.016 1.065a1.547 1.547 0 00.376 1.45l4.249 4.42H1.689C.895 7.49.25 8.162.25 8.99s.645 1.5 1.439 1.5h10.943l-4.249 4.42a1.547 1.547 0 00-.311 1.634c.222.56.746.925 1.328.926a1.394 1.394 0 001.016-.47l6.714-7a1.546 1.546 0 000-2.12v.06z" fill="#fff"></path></svg></button>
        <div class="reutilizaveis__FloorBottom-sc-1rpqs51-7 fJZlHt">
            <div class="reutilizaveis__FloorBottomText-sc-1rpqs51-8 iuHXUn">Equipe TryEdu.</div>
        </div>';
    }
  ?>
<!DOCTYPE html>
<html lang="pt-BR"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width">
<meta charset="utf-8"><title>TryEdu</title>
<meta name="description" content="JOCKEY"><link rel="shortcut icon" id="favicon" href="http://localhost:3000/favicon.png"><link rel="apple-touch-icon" href="http://localhost:3000/img/icon-512.png"><link rel="manifest" href="http://localhost:3000/manifest.json">
<meta name="next-head-count" content="7"><noscript data-n-css=""></noscript><noscript id="__next_css__DO_NOT_USE__"></noscript>
<style data-styled="active" data-styled-version="5.3.3">@import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');*{margin:0;padding:0;box-sizing:border-box;}html{font-size:90.5%;}a{color:inherit;-webkit-text-decoration:none;text-decoration:none;}body{font-family:'Montserrat',sans-serif;}.bOvseR{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;-webkit-align-items:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;background:#fcfcfc;width:100vw;height:100vh;background-repeat:no-repeat;background-position:top center;}.errEjS{width:100%;height:auto;text-align:center;margin-top:2rem;font-family:Montserrat;font-style:normal;font-weight:600;font-size:1.5rem;line-height:30px;color:#a9c2cd;}.cGyiGr{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;top:8rem;width:100%;height:9.5rem;margin-top:2rem;margin-bottom:6rem;}.eefkls{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;width:80%;margin:0 auto;margin-top:1rem;max-height:4rem;max-width:30rem;}.cxnaMm{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;-webkit-align-items:flex-start;-webkit-box-align:flex-start;-ms-flex-align:flex-start;align-items:flex-start;padding:0px 16px;width:100%;height:48px;background:rgba(169,194,205,0.1);border:1px solid #a9c2cd;box-sizing:border-box;border-radius:8px;}.BcyMz{font-size:2.4rem;border:none;margin:none;-webkit-align-self:left;-ms-flex-item-align:left;align-self:left;}.dRzqWT{width:19.5rem;height:3.9rem;background:#4992bf;font-size:1.25rem;text-align:center;color:#fff;font-weight:600;line-height:49px;cursor:pointer;box-shadow:0px 4px 10px rgba(0,0,0,0.2);border-radius:15px;z-index:1;}.jlohmp{position:absolute;background:#33cc33;box-shadow:inset 0px 4px 4px rgba(255,252,252,0.26);border-radius:40px;width:30%;height:0.875rem;z-index:1;}.hqAUWN{position:absolute;width:70%;height:0.875rem;background:#ddd;border-radius:40px;}.fJZlHt{position:fixed;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-align-items:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;width:102vw;height:6.875rem;bottom:0px;left:0.05rem;padding:10%;background:#e7f3ff;box-shadow:inset 0px 4px 10px #f2f2f2;border-radius:0 0 40px 40px;}.iuHXUn{width:80%;height:auto;font-style:normal;font-weight:normal;font-size:12px;line-height:18px;text-align:center;color:#a9c2cd;}.jMkbYK{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;-webkit-align-items:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;background:linear-gradient( 360deg,#1c3458 14.29%,#164773 46.9%,rgb(34,55,92) 69.52% );min-width:100vw;min-height:100vh;background-repeat:no-repeat;background-position:top center;}.bsYvyd{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-align-items:flex-end;-webkit-box-align:flex-end;-ms-flex-align:flex-end;align-items:flex-end;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;height:50vh;width:100%;}.cATufW{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;-webkit-align-items:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;height:auto;width:100%;}.gKtwZF{margin:0 auto;margin-bottom:-14rem;}.iogliI{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;width:78%;margin-top:1rem;max-height:4rem;max-width:30rem;}.gOIGMN{height:2.8rem;padding:1rem;border:1px solid #ffffff;background:rgba(255,255,255,0.1);border-radius:10px;color:#fff;-webkit-letter-spacing:0.2px;-moz-letter-spacing:0.2px;-ms-letter-spacing:0.2px;letter-spacing:0.2px;outline:none;-webkit-transition:0.6s;transition:0.6s;}.gOIGMN:focus{border:2px solid blue;}.gGnvCO{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;width:100%;text-align:center;-webkit-box-pack:justify;-webkit-justify-content:space-between;-ms-flex-pack:justify;justify-content:space-between;}.izUqXv{height:6rem;width:78%;padding:1rem;text-align:justify;border:none;background:rgba(255,255,255,0.1);border-radius:10px;color:#fff;-webkit-letter-spacing:0.2px;-moz-letter-spacing:0.2px;-ms-letter-spacing:0.2px;letter-spacing:0.2px;outline:none;-webkit-transition:0.6s;transition:0.6s;}.jCuLWe{position:absolute;display:none;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;-webkit-align-self:flex-end;-ms-flex-item-align:end;align-self:flex-end;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;height:2.8rem;font-size:1.4rem;margin-right:0.6rem;color:var(--text-menu);outline:none;background-color:transparent;border:none;}.iAybqz{position:absolute;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;-webkit-align-self:flex-end;-ms-flex-item-align:end;align-self:flex-end;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;height:2.8rem;font-size:1.4rem;margin-right:0.6rem;color:var(--text-menu);outline:none;background-color:transparent;border:none;}.lmnQZl{color:#254ddb;font-weight:bold;outline:none;border:none;background:white;border-radius:10px;-webkit-transition:0.5s;transition:0.5s;text-align:center;height:2.8rem;padding:1rem;}.lmnQZl:focus{border:2px solid blue;}.hmfMQO{padding-top:0rem;font-size:1.4rem;color:white;text-align:center;vertical-align:middle;line-height:3rem;font-weight:bold;min-height:3rem;max-height:5rem;width:108%;background-color:gray;border-radius:1rem;}.bxKgZO{background:#fcfcfc;width:100%;height:100vh;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;-webkit-align-items:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;}.eccTeN{width:auto;height:auto;font-family:Montserrat;font-style:normal;font-weight:normal;font-size:20px;line-height:60px;text-align:center;vertical-align:baseline;}.cvlIMy{-webkit-align-items:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;top:11rem;width:100%;height:auto;}.caTexg{height:80px;width:190px;margin:20px 0 0 30px;padding-top:15px;box-shadow:0px 2px 15px rgba(0,0,0,0.1);border-radius:15px;-webkit-transform:matrix(1,0,0.3,1,-1,0);-ms-transform:matrix(1,0,0.3,1,-1,0);transform:matrix(1,0,0.3,1,-1,0);background-color:#fff;z-index:1;}.cgfNyB{width:150px;height:auto;margin:0 auto;font-size:13px;line-height:16px;color:#4992bf;-webkit-transform:matrix(1,0,-0.3,1,1,0);-ms-transform:matrix(1,0,-0.3,1,1,0);transform:matrix(1,0,-0.3,1,1,0);}.ehXZDe{display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;font-size:20px;margin:0 auto;width:300px;color:#4992bf;line-height:60px;height:60px;text-align:center;box-shadow:0px 2px 15px rgba(0,0,0,0.1);border-radius:0px 0px 10px 10px;}</style><style type="text/css">/* Copyright 2014-present Evernote Corporation. All rights reserved. */
@keyframes caretBlink {
    from { opacity: 1.0; }
    to { opacity: 0.0; }
}

@keyframes rotateSpinner {
    from {
        transform:rotate(0deg);
    }
    to {
        transform:rotate(360deg);
    }
}

#text-tool-caret {
    animation-name: caretBlink;  
    animation-iteration-count: infinite;  
    animation-timing-function: cubic-bezier(1.0,0,0,1.0);
    animation-duration: 1s; 
}

#en-markup-loading-spinner {
    position: absolute;
    top: calc(50% - 16px);
    left: calc(50% - 16px);
    width: 32px;
    height: 32px;
}

#en-markup-loading-spinner img {
    position: relative;
    top: 0px;
    left: 0px;
    animation-name: rotateSpinner;
    animation-duration: 0.6s;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
}
</style><style type="text/css">/* Copyright 2014-present Evernote Corporation. All rights reserved. */
.skitchToastBoxContainer {
    position: absolute;
    width: 100%;
    text-align: center;
    top: 30px;
    -webkit-user-select: none;
    -moz-user-select: none;
    pointer-events: none;
}

.skitchToastBox {
    width: 200px;
    height: 16px;
    padding: 12px;
    background-color: rgba(47, 55, 61, 0.95);
    border-radius: 4px;
    color: white;
    cursor: default;
    font-size: 10pt;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.32);
    font-family: 'Soleil', Helvetica, Arial, sans-serif;
    border: 2px rgba(255, 255, 255, 0.38) solid;
}

.lang-zh-cn .skitchToastBox {
    font-family: '微软雅黑', 'Microsoft YaHei', SimSun,
        '&#x30E1;&#x30A4;&#x30EA;&#x30AA;', Meiryo, 'MS PGothic', 'Soleil',
        Helvetica, Arial, sans-serif;
}

.lang-ja-jp .skitchToastBox {
    font-family: '&#x30E1;&#x30A4;&#x30EA;&#x30AA;', Meiryo, 'MS PGothic',
        '微软雅黑', 'Microsoft YaHei', SimSun, 'Soleil', Helvetica, Arial,
        sans-serif;
}

.skitchToast {
    padding-left: 20px;
    padding-right: 20px;
    display: inline-block;
    height: 10px;
    color: #f1f5f8;
    text-align: center;
}

.skitchVisible {
    /* Don't remove this class it's a hack used by the Evernote Clipper */
}
</style><style type="text/css">/* Copyright 2014-present Evernote Corporation. All rights reserved. */

@font-face {
	font-family: 'Soleil';
    background-position: 65% 50%;
    background-repeat: no-repeat;
}

#en-markup-alert-container .cell-2 {
    position: relative;
    float: left;
    width: 345px;
    margin-top: 29px;
    margin-bottom: 20px;
}

#en-markup-alert-container .cell-2 .cell-2-title {
    margin-bottom: 5px;
    padding-right: 30px;
    font-size: 12pt;
    font-family: Tahoma, Arial;
}

#en-markup-alert-container .cell-2 .cell-2-message {
    padding-right: 30px;
    font-size: 9.5pt;
    font-family: Tahoma, Arial;
}

#en-markup-alert-container .cell-3 {
    position: relative;
    width: 450px;
    height: 60px;
    float: left;
    background-color: rgb(240,240,240);
}

#en-markup-alert-container .cell-3 button {
    position: absolute;
    top: 12px;
    right: 15px;
    width: 110px;
    height: 36px;
}

#en-markup-alert-container .cell-3 button.alt-button {
    position: absolute;
    top: 12px;
    right: 140px;
    width: 110px;
    height: 36px;
}
</style></head>
<body><div id="__next" data-reactroot="">
<form method="get" class="informeDados__Container-sc-akle90-0 bOvseR" onSubmit="return testaSenha()">
    <div style="display: flex; place-self: flex-start; margin-top: 3rem;">
        <div style="margin-top: 0.8rem;">
            <div style="width: 80%; margin-left: 10%;" class="reutilizaveis__Navigation_Ative_Bom-sc-1rpqs51-4 jlohmp">
            </div>
        </div>
    </div>
    <?php
    echo $texto;
?>
    </form>
    </div>
    <div id="chakra-toast-portal">
        <ul id="chakra-toast-manager-top" style="position: fixed; z-index: 5500; pointer-events: none; display: flex; flex-direction: column; margin: 0px auto; top: env(safe-area-inset-top, 0px); right: env(safe-area-inset-right, 0px); left: env(safe-area-inset-left, 0px);"></ul><ul id="chakra-toast-manager-top-left" style="position: fixed; z-index: 5500; pointer-events: none; display: flex; flex-direction: column; top: env(safe-area-inset-top, 0px); left: env(safe-area-inset-left, 0px);"></ul><ul id="chakra-toast-manager-top-right" style="position: fixed; z-index: 5500; pointer-events: none; display: flex; flex-direction: column; top: env(safe-area-inset-top, 0px); right: env(safe-area-inset-right, 0px);"></ul><ul id="chakra-toast-manager-bottom-left" style="position: fixed; z-index: 5500; pointer-events: none; display: flex; flex-direction: column; bottom: env(safe-area-inset-bottom, 0px); left: env(safe-area-inset-left, 0px);"></ul><ul id="chakra-toast-manager-bottom" style="position: fixed; z-index: 5500; pointer-events: none; display: flex; flex-direction: column; margin: 0px auto; bottom: env(safe-area-inset-bottom, 0px); right: env(safe-area-inset-right, 0px); left: env(safe-area-inset-left, 0px);"></ul><ul id="chakra-toast-manager-bottom-right" style="position: fixed; z-index: 5500; pointer-events: none; display: flex; flex-direction: column; bottom: env(safe-area-inset-bottom, 0px); right: env(safe-area-inset-right, 0px);">
        </ul></div>
    <script>
        function testaSenha(){
            if (password.value == password2.value){
                return true;
            } else {
                alert('As senhas digitadas são diferentes.');
                password.focus();
                return false;
            }
        }
    </script>
</body>
</html>