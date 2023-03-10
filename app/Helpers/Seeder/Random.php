<?php

namespace App\Helpers\Seeder;

use Illuminate\Support\Arr;

class Random
{
    /**
     * Links de imagens online para testes de banco de dados
     *
     * @return string
     */
    public static function image(): string
    {
        return Arr::random([
            'https://images.pexels.com/photos/355508/pexels-photo-355508.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500',
            'https://ciclovivo.com.br/wp-content/uploads/2018/10/iStock-536613027-1024x683.jpg',
            'https://www.estrategiaods.org.br/wp-content/uploads/2021/08/educa%C3%A7%C3%A3o-aa.png',
            'https://todospelaeducacao.org.br/wordpress/wp-content/uploads/2020/10/thumb-irs.jpg',
            'https://www.ituporanga.sc.gov.br/media/noticia/secretaria-da-educacao-orienta-alunos-para-ensino-presencial-e-a-distancia-em-virtude-do-covid-19-3249.jpg?w=1200&h=650&t=C&c=fff&q=80',
            'https://thumbs.dreamstime.com/b/conceito-ingl%C3%AAs-da-educa%C3%A7%C3%A3o-de-l%C3%ADngua-de-ingleses-inglaterra-58368527.jpg',
            'https://i0.wp.com/blog.portaleducacao.com.br/wp-content/uploads/2022/02/365-O-que-e%CC%81-tecnologia_.jpg',
            'https://cdn.diariocalifornia.com/wp-content/uploads/2021/05/tecnologia.jpg',
            'https://png.pngtree.com/thumb_back/fh260/background/20210326/pngtree-computer-desktop-background-map-image_590916.jpg',
            'https://static.vecteezy.com/ti/fotos-gratis/p1/2212992-conjunto-de-ferramentas-de-trabalho-com-um-bloco-de-notas-em-um-fundo-de-madeira-foto.jpg',
            'https://azimute.med.br/wp-content/uploads/cursos-online.png',
            'https://www.licitanordeste.com.br/img/cursos.png',
            'https://idocode.com.br/wp-content/uploads/2021/07/programacao-scaled.jpg',
            'https://mentorama.com.br/blog/wp-content/uploads/2022/06/capa-blog-coding-iniciante.jpg',
            'https://dkrn4sk0rn31v.cloudfront.net/uploads/2018/09/09011822/capa-linguagens-em-alta.jpg',
            'https://vangardi.com.br/wp-content/uploads/2021/07/tipos-investimentos-crescimento.jpg',
            'https://classic.exame.com/wp-content/uploads/2022/08/fluxo-de-caixa-de-investimentos.jpg?quality=70&strip=info&w=724',
            'https://classic.exame.com/wp-content/uploads/2021/01/bolsa-ou-rendafixa-btg.jpg?quality=70&strip=info&w=680',
            'https://www.v10i.com.br/wp-content/uploads/2020/08/10-investimentos-melhores-que-a-poupan%C3%A7a-simples-e-seguros.jpg',
            'https://s4.static.brasilescola.uol.com.br/be/2022/11/ilustracao-de-varios-elementos-caracteristicos-da-matematica-calculadora-grafico-compasso-numero-pi-cone-lapis-etc.jpg',
            'http://www.iic.uam.es/wp-content/uploads/2021/03/DIA-PI.jpg',
            'https://imo.com.br/wp-content/uploads/2016/11/livros-digitais.jpg',
            'https://www.estudarfora.org.br/wp-content/uploads/2013/10/20131029_livros_115791277.jpg',
            'https://metodo4ponto2.com/wp-content/uploads/2022/08/5-dicas-para-manter-a-motivacao-nos-estudos_20200607101420_4667.jpg',
            'https://img.freepik.com/fotos-gratis/conceito-de-educacao-estudante-estudando-e-brainstorming-conceito-de-campus-perto-de-estudantes-discutindo-seu-assunto-em-livros-ou-livros-didaticos-foco-seletivo_1418-627.jpg?auto=format&h=200',
            'https://img.freepik.com/fotos-gratis/pessoas-anonimas-que-estudam-juntas_23-2147656366.jpg',
            'https://s1.static.brasilescola.uol.com.br/be/conteudo/images/para-conseguir-um-bom-rendimento-nos-estudos-sao-necessarios-foco-concentracao-organizacao-5bf68360e4b3c.jpg',
            'https://i0.wp.com/labfinprovarfia.com.br/wp-content/uploads/2021/02/shutterstock_1676998303-scaled.jpg?resize=1080%2C675&ssl=1',
            'https://blog.portalpos.com.br/app/uploads/2022/02/%C3%A1reas-do-Marketing-Digita.jpg',
            'https://verticis.com.br/wp-content/uploads/2021/04/marketing-digital-como-comecar-e1639072480633.jpg',
        ]);
    }
}
