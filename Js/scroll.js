//vou colocar comentarios em todas as partes que eu adicionar ao codigo 
//assisti um video https://youtu.be/adqwnu3gs2k?si=es67JXm5SMHSF6YO
//evelyn
const observer = new IntersectionObserver((entries)=>{
    entries.forEach((entry) => {
        if (entry.isIntersecting){
            entry.target.classList.add("show")
        }else {
            entry.target.classList.remove("show")
        }
    })

    }, {})
    const todoElement = document.querySelectorAll(".todo")
    todoElement.forEach(element => observer.observe(element))